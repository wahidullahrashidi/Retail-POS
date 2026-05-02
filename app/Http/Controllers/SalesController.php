<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Loan;
use App\Models\InventoryAdjustment;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE — blade view with stats
    // ══════════════════════════════════════════
    public function page()
    {
        $today = today();

        $stats = [
            'today_revenue' => Sale::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount'),
            'today_count'   => Sale::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'today_cash'    => Sale::whereDate('created_at', $today)->where('status', 'completed')->where('payment_method', 'cash')->sum('total_amount'),
            'today_loan'    => Sale::whereDate('created_at', $today)->where('status', 'completed')->where('payment_method', 'loan')->sum('total_amount'),
            'today_refunds' => Sale::whereDate('created_at', $today)->where('status', 'refunded')->count(),
            'today_avg'     => 0,
        ];

        if ($stats['today_count'] > 0) {
            $stats['today_avg'] = $stats['today_revenue'] / $stats['today_count'];
        }

        // $cashiers = User::whereHas('shifts')->orderBy('name')->get(['id', 'name']);

        return view('sales.sales', compact('stats'));
    }

    // ══════════════════════════════════════════
    //  INDEX — paginated JSON list
    //  GET /pos/sales
    // ══════════════════════════════════════════
    public function index(Request $request)
    {
        $q        = $request->input('q', '');
        $from     = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to       = $request->input('to')   ? Carbon::parse($request->input('to'))->endOfDay()     : null;
        $method   = $request->input('method', '');
        $status   = $request->input('status', '');
        $cashier  = $request->input('cashier', '');
        $tab      = $request->input('tab', 'today');
        $sortCol  = $request->input('sort', 'created_at');
        $sortDir  = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $sortMap  = [
            'local_id'     => 'sales.local_id',
            'created_at'   => 'sales.created_at',
            'total_amount' => 'sales.total_amount',
        ];
        $orderBy = $sortMap[$sortCol] ?? 'sales.created_at';

        $query = Sale::query()
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->leftJoin('users',     'users.id',     '=', 'sales.user_id')
            ->select([
                'sales.id',
                'sales.local_id',
                'sales.sale_type',
                'sales.status',
                'sales.payment_method',
                'sales.subtotal',
                'sales.discount_amount',
                'sales.tax_amount',
                'sales.total_amount',
                'sales.amount_paid',
                'sales.change_amount',
                'sales.hold_code',
                'sales.notes',
                'sales.created_at',
                'customers.name as customer',
                'customers.phone as customer_phone',
                'users.name as cashier',
            ]);

        // ── Tab filters ──
        match ($tab) {
            'today'    => $query->whereDate('sales.created_at', today()),
            'held'     => $query->where('sales.status', 'held'),
            'refunded' => $query->where('sales.status', 'refunded'),
            default    => null,
        };

        // ── Search ──
        if ($q) {
            $query->where(fn($qb) =>
                $qb->where('sales.local_id', 'like', "%{$q}%")
                   ->orWhere('customers.name', 'like', "%{$q}%")
                   ->orWhere('sales.hold_code', 'like', "%{$q}%")
            );
        }

        // ── Date range ──
        if ($from) $query->where('sales.created_at', '>=', $from);
        if ($to)   $query->where('sales.created_at', '<=', $to);

        // ── Filters ──
        if ($method)  $query->where('sales.payment_method', $method);
        if ($status)  $query->where('sales.status', $status);
        if ($cashier) $query->where('sales.user_id', $cashier);

        $paginated = $query->orderBy($orderBy, $sortDir)->paginate(25);

        $items = collect($paginated->items())->map(fn($s) => [
            'id'             => $s->id,
            'local_id'       => $s->local_id,
            'sale_type'      => $s->sale_type,
            'status'         => $s->status,
            'payment_method' => $s->payment_method,
            'subtotal'       => (float)$s->subtotal,
            'discount_amount'=> (float)$s->discount_amount,
            'tax_amount'     => (float)$s->tax_amount,
            'total_amount'   => (float)$s->total_amount,
            'amount_paid'    => (float)$s->amount_paid,
            'change_amount'  => (float)$s->change_amount,
            'hold_code'      => $s->hold_code,
            'notes'          => $s->notes,
            'customer'       => $s->customer,
            'customer_phone' => $s->customer_phone,
            'cashier'        => $s->cashier,
            'date'           => Carbon::parse($s->created_at)->format('d M Y'),
            'time'           => Carbon::parse($s->created_at)->format('h:i A'),
        ]);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // ══════════════════════════════════════════
    //  ITEMS — sale line items for detail panel
    //  GET /pos/sales/{sale}/items
    // ══════════════════════════════════════════
    public function items(Sale $sale)
    {
        $items = $sale->saleItems()
            ->with('variant.product')
            ->get()
            ->map(fn($item) => [
                'id'           => $item->id,
                'variant_id'   => $item->variant_id,
                'product_name' => $item->variant->product->name ?? 'Unknown',
                'sku'          => $item->variant->sku ?? '—',
                'quantity'     => $item->quantity,
                'unit_price'   => (float)$item->unit_price,
                'cost_price'   => (float)($item->cost_price ?? 0),
                'discount_amount'=> (float)$item->discount_amount,
                'line_total'   => (float)$item->line_total,
                'is_returned'  => (bool)$item->is_returned,
                'returned_qty' => (int)$item->returned_qty,
            ]);

        return response()->json($items);
    }

    // ══════════════════════════════════════════
    //  REFUND — process a full or partial refund
    //  POST /pos/sales/refund
    // ══════════════════════════════════════════
    public function refund(Request $request)
    {
        $request->validate([
            'sale_id'          => 'required|integer|exists:sales,id',
            'items'            => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|integer|exists:sale_items,id',
            'items.*.qty'      => 'required|integer|min:1',
            'reason'           => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::with('saleItems.variant', 'loan')->findOrFail($request->sale_id);

            if ($sale->status === 'refunded') {
                throw new \Exception('This sale has already been refunded.');
            }

            if ($sale->status !== 'completed') {
                throw new \Exception('Only completed sales can be refunded.');
            }

            $refundTotal = 0;

            foreach ($request->items as $refundItem) {
                $saleItem = $sale->saleItems->firstWhere('id', $refundItem['sale_item_id']);

                if (!$saleItem) {
                    throw new \Exception("Sale item not found.");
                }

                $maxRefundable = $saleItem->quantity - $saleItem->returned_qty;
                if ($refundItem['qty'] > $maxRefundable) {
                    throw new \Exception("Refund qty exceeds available qty for {$saleItem->variant->sku}.");
                }

                // ── Restore stock ────────────────────
                $variant = ProductVariant::lockForUpdate()->findOrFail($saleItem->variant_id);
                $prevStock = $variant->stock_quantity;
                $newStock  = $prevStock + $refundItem['qty'];
                $variant->update(['stock_quantity' => $newStock]);

                // Log the adjustment
                InventoryAdjustment::create([
                    'variant_id'      => $variant->id,
                    'adjustment_type' => 'return_to_supplier',
                    'quantity'        => $refundItem['qty'],
                    'reason'          => "Refund of sale {$sale->local_id}: {$request->reason}",
                    'reference_type'  => Sale::class,
                    'reference_id'    => $sale->id,
                    'adjusted_by'     => auth()->id(),
                    'previous_stock'  => $prevStock,
                    'new_stock'       => $newStock,
                ]);

                // ── Mark sale item as returned ───────
                $saleItem->update([
                    'is_returned'  => ($refundItem['qty'] === $saleItem->quantity),
                    'returned_qty' => $saleItem->returned_qty + $refundItem['qty'],
                ]);

                // Calculate refund amount for this item
                $unitPrice   = $saleItem->unit_price;
                $refundTotal += $unitPrice * $refundItem['qty'];
            }

            // ── Update sale status ───────────────────
            $allReturned = $sale->saleItems->every(
                fn($i) => $i->fresh()->returned_qty >= $i->quantity
            );
            $sale->update([
                'status'   => $allReturned ? 'refunded' : 'completed',
                'notes'    => ($sale->notes ? $sale->notes . ' | ' : '') . "Partial refund: {$request->reason}",
            ]);

            // ── Adjust loan if applicable ────────────
            if ($sale->payment_method === 'loan' && $sale->loan) {
                $loan = $sale->loan;
                // Reduce the loan balance by the refund amount
                $newRemaining = max(0, $loan->remaining_balance - $refundTotal);
                $newPaid      = max(0, $loan->original_amount - $newRemaining);
                $loan->update([
                    'remaining_balance' => $newRemaining,
                    'amount_paid'       => $newPaid,
                    'status'            => $newRemaining <= 0 ? 'paid' : $loan->status,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'refund_total' => $refundTotal,
                'fully_refunded' => $allReturned,
                'message'      => $allReturned
                    ? 'Sale fully refunded and stock restored.'
                    : "Partial refund of Af {$refundTotal} processed.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ══════════════════════════════════════════
    //  EXPORT — CSV download
    //  GET /pos/sales/export
    // ══════════════════════════════════════════
    public function export(Request $request)
    {
        $from   = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : today()->startOfDay();
        $to     = $request->input('to')   ? Carbon::parse($request->input('to'))->endOfDay()     : today()->endOfDay();
        $tab    = $request->input('tab', 'all');
        $method = $request->input('method', '');
        $status = $request->input('status', '');

        $sales = Sale::query()
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->leftJoin('users',     'users.id',     '=', 'sales.user_id')
            ->select([
                'sales.local_id', 'sales.sale_type', 'sales.status',
                'sales.payment_method', 'sales.subtotal', 'sales.discount_amount',
                'sales.total_amount', 'sales.amount_paid', 'sales.change_amount',
                'sales.created_at', 'customers.name as customer', 'users.name as cashier',
            ])
            ->whereBetween('sales.created_at', [$from, $to])
            ->when($tab === 'today',    fn($q) => $q->whereDate('sales.created_at', today()))
            ->when($tab === 'held',     fn($q) => $q->where('sales.status', 'held'))
            ->when($tab === 'refunded', fn($q) => $q->where('sales.status', 'refunded'))
            ->when($method, fn($q) => $q->where('sales.payment_method', $method))
            ->when($status, fn($q) => $q->where('sales.status', $status))
            ->orderByDesc('sales.created_at')
            ->get();

        $filename = 'sales-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Sale ID', 'Type', 'Status', 'Method', 'Customer', 'Cashier', 'Subtotal', 'Discount', 'Total', 'Paid', 'Change', 'Date']);
            foreach ($sales as $s) {
                fputcsv($handle, [
                    $s->local_id, $s->sale_type, $s->status,
                    $s->payment_method, $s->customer ?? 'Walk-in', $s->cashier,
                    $s->subtotal, $s->discount_amount, $s->total_amount,
                    $s->amount_paid, $s->change_amount,
                    Carbon::parse($s->created_at)->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
