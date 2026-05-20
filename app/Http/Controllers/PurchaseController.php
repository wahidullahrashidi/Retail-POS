<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ProductVariant;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PurchasePayment;

class PurchaseController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — paginated PO JSON
    //  GET /pos/purchases
    // ══════════════════════════════════════════
    public function index(Request $request)
{
    $q       = $request->input('q', '');
    $status  = $request->input('status', '');
    $payment = $request->input('payment', '');

    // Pre‑aggregate totals in a single derived table
    $itemAgg = PurchaseItem::selectRaw('purchase_id,
        SUM(quantity_ordered) as total_ordered,
        SUM(quantity_received) as total_received')
        ->groupBy('purchase_id');

    $query = Purchase::query()
        ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
        ->leftJoin('users', 'users.id', '=', 'purchases.created_by')
        ->leftJoinSub($itemAgg, 'item_agg', fn($join) =>
            $join->on('item_agg.purchase_id', '=', 'purchases.id')
        )
        ->select([
            'purchases.id',
            'purchases.local_id',
            'purchases.reference_number',
            'purchases.purchase_date',
            'purchases.delivery_date',
            'purchases.status',
            'purchases.total_cost',
            'purchases.amount_paid',
            'purchases.payment_status',
            'purchases.notes',
            'suppliers.name as supplier',
            'users.name as created_by_name',
            DB::raw('COALESCE(item_agg.total_ordered, 0) as total_ordered'),
            DB::raw('COALESCE(item_agg.total_received, 0) as total_received'),
        ]);

    if ($q) {
        $query->where(function ($qb) use ($q) {
            $qb->where('purchases.local_id', 'like', "%{$q}%")
               ->orWhere('suppliers.name', 'like', "%{$q}%")
               ->orWhere('purchases.reference_number', 'like', "%{$q}%");
        });
    }

    if ($status)  $query->where('purchases.status', $status);
    if ($payment) $query->where('purchases.payment_status', $payment);

    $paginated = $query->orderByDesc('purchases.purchase_date')->paginate(20);

    $items = $paginated->getCollection()->map(fn($p) => [
        'id'               => $p->id,
        'local_id'         => $p->local_id,
        'reference_number' => $p->reference_number,
        'purchase_date'    => Carbon::parse($p->purchase_date)->format('d M Y'),
        'delivery_date'    => $p->delivery_date ? Carbon::parse($p->delivery_date)->format('d M Y') : null,
        'status'           => $p->status,
        'total_cost'       => (float) $p->total_cost,
        'amount_paid'      => (float) $p->amount_paid,
        'payment_status'   => $p->payment_status,
        'notes'            => $p->notes,
        'supplier'         => $p->supplier,
        'received_pct'     => $p->total_ordered > 0
            ? round(($p->total_received / $p->total_ordered) * 100)
            : 0,
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
    //  PO ITEMS — for detail panel
    //  GET /pos/purchases/{purchase}/items
    // ══════════════════════════════════════════
    public function items(Purchase $purchase)
    {
        $items = PurchaseItem::where('purchase_id', $purchase->id)
            ->join('product_variants', 'product_variants.id', '=', 'purchase_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->select([
                'purchase_items.id',
                'purchase_items.variant_id',
                'purchase_items.quantity_ordered',
                'purchase_items.quantity_received',
                'purchase_items.unit_cost',
                'purchase_items.line_total',
                'purchase_items.expiry_date',
                'purchase_items.batch_number',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.stock_quantity',
            ])
            ->get()
            ->map(fn($i) => [
                'id'                => $i->id,
                'variant_id'        => $i->variant_id,
                'product_name'      => $i->product_name,
                'sku'               => $i->sku,
                'quantity_ordered'  => (int)$i->quantity_ordered,
                'quantity_received' => (int)$i->quantity_received,
                'unit_cost'         => (float)$i->unit_cost,
                'line_total'        => (float)$i->line_total,
                'expiry_date'       => $i->expiry_date,
                'batch_number'      => $i->batch_number,
                'current_stock'     => (int)$i->stock_quantity,
            ]);

        return response()->json($items);
    }

    // ══════════════════════════════════════════
    //  RECEIVE STOCK
    //  POST /pos/purchases/receive
    // ══════════════════════════════════════════
    public function receive(Request $request)
{
    $request->validate([
        'purchase_id'                  => 'required|integer|exists:purchases,id',
        'items'                        => 'required|array|min:1',
        'items.*.purchase_item_id'     => 'required|integer|exists:purchase_items,id',
        'items.*.qty'                  => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $purchase = Purchase::findOrFail($request->purchase_id);

        if ($purchase->status === 'received') {
            throw new \Exception('This purchase order is already fully received.');
        }
        if ($purchase->status === 'cancelled') {
            throw new \Exception('Cannot receive a cancelled purchase order.');
        }

        // Build incoming items keyed by purchase_item_id
        $incoming = collect($request->items)->keyBy('purchase_item_id');

        // 1) Lock all needed purchase items in ONE query
        $poItems = PurchaseItem::lockForUpdate()
            ->where('purchase_id', $purchase->id)
            ->whereIn('id', $incoming->keys())
            ->get()
            ->keyBy('id');

        // 2) Lock all corresponding variants in ONE query
        $variantIds = $poItems->pluck('variant_id')->unique();
        $variants = ProductVariant::lockForUpdate()
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        // Track whether all items are now fully received
        $allFullyReceived = true;   // start optimistic; we'll disprove if needed

        foreach ($incoming as $itemId => $receive) {
            $poItem = $poItems->get($itemId);
            if (! $poItem) {
                throw new \Exception("Item ID {$itemId} not found in this PO.");
            }

            $maxReceivable = $poItem->quantity_ordered - $poItem->quantity_received;
            if ($receive['qty'] > $maxReceivable) {
                throw new \Exception("Cannot receive more than ordered for item {$poItem->id}. Max: {$maxReceivable}");
            }

            $variant = $variants->get($poItem->variant_id);
            $prevStock = $variant->stock_quantity;
            $newStock  = $prevStock + $receive['qty'];

            // Update variant stock
            $variant->update([
                'stock_quantity' => $newStock,
                'expiry_date'    => $poItem->expiry_date ?? $variant->expiry_date,
                'batch_number'   => $poItem->batch_number ?? $variant->batch_number,
            ]);

            // Log adjustment
            InventoryAdjustment::create([
                'variant_id'      => $variant->id,
                'adjustment_type' => 'increase',
                'quantity'        => $receive['qty'],
                'reason'          => "Received from PO {$purchase->local_id}",
                'reference_type'  => Purchase::class,
                'reference_id'    => $purchase->id,
                'adjusted_by'     => auth()->id(),
                'previous_stock'  => $prevStock,
                'new_stock'       => $newStock,
            ]);

            // Update the purchase item received quantity
            $newReceived = $poItem->quantity_received + $receive['qty'];
            $poItem->update(['quantity_received' => $newReceived]);

            // If this item is not yet fully received, the PO is not fully received
            if ($newReceived < $poItem->quantity_ordered) {
                $allFullyReceived = false;
            }
        }

        // Determine new PO status from memory – no extra queries
        $anyReceived = $poItems->contains(fn($item) => $item->quantity_received > 0);
        $newStatus = $allFullyReceived ? 'received' : ($anyReceived ? 'partial' : 'ordered');

        $purchase->update([
            'status'      => $newStatus,
            'received_by' => auth()->id(),
        ]);

        DB::commit();

        return response()->json([
            'success'      => true,
            'new_status'   => $newStatus,
            'all_received' => $allFullyReceived,
            'message'      => $allFullyReceived
                ? 'All items received. Stock updated.'
                : 'Partial receipt recorded. Stock updated.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
}

    // ══════════════════════════════════════════
    //  CANCEL PO
    //  POST /pos/purchases/{purchase}/cancel
    // ══════════════════════════════════════════
    public function cancel(Purchase $purchase)
    {
        if ($purchase->status !== 'ordered') {
            return response()->json([
                'success' => false,
                'message' => 'Only orders with status "ordered" can be cancelled.',
            ], 422);
        }

        $purchase->update(['status' => 'cancelled']);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  RECORD PURCHASE PAYMENT
    //  POST /pos/purchases/payment
    // ══════════════════════════════════════════

    public function storePayment(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|integer|exists:purchases,id',
            'amount'      => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:500',
        ]);

        $purchase = Purchase::findOrFail($request->purchase_id);

        // prevent cancelled purchase payments
        if ($purchase->status === 'cancelled') {

            return response()->json([
                'success' => false,
                'message' => 'Cannot pay cancelled purchase.',
            ], 422);
        }

        $remaining = $purchase->total_cost - $purchase->amount_paid;

        // prevent overpayment
        if ($request->amount > $remaining) {

            return response()->json([
                'success' => false,
                'message' => 'Payment exceeds remaining balance.',
            ], 422);
        }

        DB::transaction(function () use ($request, $purchase) {

            // save payment history
            PurchasePayment::create([
                'purchase_id'     => $purchase->id,
                'amount'          => $request->amount,
                'payment_method'  => 'cash',
                'payment_date'    => now(),
                'reference_number' => null,
                'notes'           => $request->notes,
                'created_by'      => auth()->id(),
            ]);

            // recalculate paid amount
            $paid = $purchase->payments()->sum('amount');

            $remaining = $purchase->total_cost - $paid;

            // update summary fields
            $purchase->update([
                'amount_paid' => $paid,
                'payment_status' => $remaining <= 0
                    ? 'paid'
                    : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);
        });

        $purchase->refresh();

        return response()->json([
            'success' => true,
            'amount_paid' => $purchase->amount_paid,
            'payment_status' => $purchase->payment_status,
            'remaining_balance' =>
            $purchase->total_cost - $purchase->amount_paid,
        ]);
    }
}
