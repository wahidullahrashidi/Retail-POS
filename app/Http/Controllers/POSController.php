<?php

namespace App\Http\Controllers;


use App\Services\SaleService;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Loan;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class POSController extends Controller
{
    public function f()
    {
        //
    }

    public function index()
    {
        return view('pos.pos_checkout');
    }


    public function storeSale(Request $request, SaleService $saleService)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:product_variants,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric',
            'payment_method' => 'required|in:cash,loan',
            'amount_paid' => 'nullable|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric',
        ]);

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->first();

        if (! $activeShift) {
            return response()->json(['error' => 'No active shift'], 400);
        }

        $sale = $saleService->createSale(
            $request->all(),
            $request->cart,
            auth()->id(),
            $activeShift->id
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'local_id' => $sale->local_id,
            'total' => $sale->total_amount,
        ]);
    }

    // ══════════════════════════════════════════════
    //  STORE — Complete a sale (cash or loan)
    // ══════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'cart'           => 'required|array|min:1',
            'cart.*.variant_id' => 'required|integer|exists:product_variants,id',
            'cart.*.qty'     => 'required|integer|min:1',
            'cart.*.price'   => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,loan',
            'customer_id'    => 'nullable|integer|exists:customers,id',
            'cash_received'  => 'required_if:payment_method,cash|numeric|min:0',
            'loan_deposit'   => 'nullable|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'discount_type'  => 'nullable|in:pct,flat',
            'tax_rate'       => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:1000',
            'is_return'      => 'boolean',
        ]);

        // Must have an active shift
        $shift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->firstOrFail();

        // Loan sales require a customer
        if ($request->payment_method === 'loan' && ! $request->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'A customer must be selected for loan sales.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // ── 1. Calculate totals ──────────────────────
            $subtotal = collect($request->cart)->sum(fn($i) => $i['price'] * $i['qty']);

            $discountAmount = 0;
            if ($request->filled('discount') && $request->discount > 0) {
                $discountAmount = $request->discount_type === 'pct'
                    ? $subtotal * ($request->discount / 100)
                    : min($request->discount, $subtotal);
            }

            $taxRate    = $request->tax_rate ?? 0;
            $taxAmount  = ($subtotal - $discountAmount) * $taxRate;
            $totalAmount = max(0, $subtotal - $discountAmount + $taxAmount);

            $amountPaid   = $request->payment_method === 'cash'
                ? min($request->cash_received, $totalAmount)   // never record overpayment as paid
                : ($request->loan_deposit ?? 0);

            $changeAmount = $request->payment_method === 'cash'
                ? max(0, $request->cash_received - $totalAmount)
                : 0;

            $saleType = $request->is_return
                ? 'return'
                : ($request->payment_method === 'loan' ? 'loan' : 'regular');

            // ── 2. Create Sale record ────────────────────
            $sale = Sale::create([
                'local_id'       => 'POS-' . strtoupper(Str::random(8)),
                'shift_id'       => $shift->id,
                'user_id'        => auth()->id(),
                'customer_id'    => $request->customer_id,
                'sale_type'      => $saleType,
                'status'         => 'completed',
                'subtotal'       => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $amountPaid,
                'change_amount'  => $changeAmount,
                'receipt_printed' => $request->print_receipt ?? true,
                'notes'          => $request->notes,
                'sync_status'    => 'pending',
            ]);

            // ── 3. Create Sale Items + deduct stock ──────
            foreach ($request->cart as $item) {
                $variant = ProductVariant::lockForUpdate()->findOrFail($item['variant_id']);

                // Stock check (skip for returns — they add stock back)
                if (! $request->is_return && $variant->stock_quantity < $item['qty']) {
                    throw new \Exception("Insufficient stock for: {$variant->sku}. Available: {$variant->stock_quantity}");
                }

                $lineDiscount = 0; // per-line discount can be added later
                $lineTotal    = ($item['price'] * $item['qty']) - $lineDiscount;

                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'variant_id'      => $variant->id,
                    'quantity'        => $item['qty'],
                    'unit_price'      => $item['price'],
                    'cost_price'      => $variant->cost_price,
                    'discount_amount' => $lineDiscount,
                    'line_total'      => $lineTotal,
                    'is_returned'     => $request->is_return,
                ]);

                // Adjust stock
                if ($request->is_return) {
                    $variant->increment('stock_quantity', $item['qty']);
                } else {
                    $variant->decrement('stock_quantity', $item['qty']);
                }
            }

            // ── 4. Create Loan record (if loan payment) ──
            $loan = null;
            if ($request->payment_method === 'loan') {
                $deposit          = $request->loan_deposit ?? 0;
                $remainingBalance = $totalAmount - $deposit;

                $loan = Loan::create([
                    'sale_id'          => $sale->id,
                    'customer_id'      => $request->customer_id,
                    'original_amount'  => $totalAmount,
                    'amount_paid'      => $deposit,
                    'remaining_balance' => $remainingBalance,
                    'due_date'         => Carbon::now()->addDays(30)->toDateString(),
                    'status'           => $remainingBalance <= 0 ? 'paid' : 'active',
                    'payment_count'    => $deposit > 0 ? 1 : 0,
                    'last_payment_at'  => $deposit > 0 ? now() : null,
                ]);

                // Link loan back to sale
                $sale->update(['loan_id' => $loan->id]);
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'sale_id'   => $sale->local_id,
                'cashier'   => auth()->user()->name,
                'change'    => $changeAmount,
                'loan_id'   => $loan?->id,
                'message'   => 'Sale completed successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    // ══════════════════════════════════════════════
    //  HOLD — Save cart as a held sale
    // ══════════════════════════════════════════════
    public function hold(Request $request)
    {
        $request->validate([
            'cart'       => 'required|array|min:1',
            'cart.*.variant_id' => 'required|integer|exists:product_variants,id',
            'cart.*.qty'  => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $shift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->firstOrFail();

        // Calculate subtotal for held sale
        $subtotal = collect($request->cart)->sum(fn($i) => $i['price'] * $i['qty']);

        // Unique hold code cashier can use to recall this cart
        $holdCode = strtoupper(Str::random(6));

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'local_id'        => 'HOLD-' . $holdCode,
                'shift_id'        => $shift->id,
                'user_id'         => auth()->id(),
                'customer_id'     => null,
                'sale_type'       => 'regular',
                'status'          => 'held',
                'subtotal'        => $subtotal,
                'discount_amount' => 0,
                'tax_amount'      => 0,
                'total_amount'    => $subtotal,
                'payment_method'  => 'cash',   // placeholder, overwritten on completion
                'amount_paid'     => 0,
                'change_amount'   => 0,
                'hold_code'       => $holdCode,
                'hold_expires_at' => Carbon::now()->addHours(4),
                'receipt_printed' => false,
                'notes'           => $request->notes,
                'sync_status'     => 'pending',
            ]);

            foreach ($request->cart as $item) {
                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'variant_id'      => $item['variant_id'],
                    'quantity'        => $item['qty'],
                    'unit_price'      => $item['price'],
                    'cost_price'      => ProductVariant::find($item['variant_id'])?->cost_price,
                    'discount_amount' => 0,
                    'line_total'      => $item['price'] * $item['qty'],
                    'is_returned'     => false,
                ]);
                // NOTE: stock is NOT deducted on hold — only on final completion
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'hold_code' => $holdCode,
                'message'   => "Sale held. Recall code: {$holdCode}",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    // ══════════════════════════════════════════════
    //  SEARCH CUSTOMERS
    // ══════════════════════════════════════════════
    public function searchCustomers(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name',  'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('phone_secondary', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            })
            ->select(['id', 'name', 'phone', 'city', 'credit_limit'])
            // Attach outstanding loan balance as a subquery
            ->withSum(
                ['loans' => fn($q) => $q->where('status', 'active')],
                'remaining_balance'
            )
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id'           => $c->id,
                'name'         => $c->name,
                'phone'        => $c->phone,
                'city'         => $c->city,
                'credit_limit' => $c->credit_limit,
                'loan_balance' => $c->loans_sum_remaining_balance ?? 0,
            ]);

        return response()->json($customers);
    }

    public function recall(Request $request)
{
    $code = strtoupper(trim($request->input('code', '')));

    if (empty($code)) {
        return response()->json(['success' => false, 'message' => 'No code provided.']);
    }

    $sale = Sale::with('saleItems.variant.product')
        ->where('hold_code', $code)
        ->where('status', 'held')
        ->where('hold_expires_at', '>', now())
        ->first();

    if (! $sale) {
        return response()->json([
            'success' => false,
            'message' => 'Hold code not found or expired.',
        ]);
    }

    // Rebuild cart array from saved sale items
    $cart = $sale->saleItems->map(fn($item) => [
        'variant_id'     => $item->variant_id,
        'name'           => $item->variant->product->name,
        'sku'            => $item->variant->sku,
        'price'          => (float) $item->unit_price,
        'qty'            => $item->quantity,
        'stock_quantity' => $item->variant->stock_quantity + $item->quantity, // add back since not deducted
        'lineTotal'      => (float) $item->line_total,
        'row_discount'   => 0,
    ]);

    // Mark as cancelled so it can't be recalled twice
    $sale->update(['status' => 'cancelled']);

    return response()->json([
        'success' => true,
        'cart'    => $cart,
    ]);
}
}
