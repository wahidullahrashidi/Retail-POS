<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ProductVariant;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $query = Purchase::query()
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->leftJoin('users', 'users.id', '=', 'purchases.created_by')
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
            ])
            ->selectRaw('
                (SELECT SUM(pi.quantity_ordered) FROM purchase_items pi WHERE pi.purchase_id = purchases.id) as total_ordered,
                (SELECT SUM(pi.quantity_received) FROM purchase_items pi WHERE pi.purchase_id = purchases.id) as total_received
            ');

        if ($q) {
            $query->where(fn($qb) =>
                $qb->where('purchases.local_id', 'like', "%{$q}%")
                   ->orWhere('suppliers.name', 'like', "%{$q}%")
                   ->orWhere('purchases.reference_number', 'like', "%{$q}%")
            );
        }

        if ($status)  $query->where('purchases.status', $status);
        if ($payment) $query->where('purchases.payment_status', $payment);

        $paginated = $query->orderByDesc('purchases.purchase_date')->paginate(20);

        $items = collect($paginated->items())->map(fn($p) => [
            'id'             => $p->id,
            'local_id'       => $p->local_id,
            'reference_number'=> $p->reference_number,
            'purchase_date'  => Carbon::parse($p->purchase_date)->format('d M Y'),
            'delivery_date'  => $p->delivery_date ? Carbon::parse($p->delivery_date)->format('d M Y') : null,
            'status'         => $p->status,
            'total_cost'     => (float)$p->total_cost,
            'amount_paid'    => (float)$p->amount_paid,
            'payment_status' => $p->payment_status,
            'notes'          => $p->notes,
            'supplier'       => $p->supplier,
            'received_pct'   => $p->total_ordered > 0
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
            $purchase = Purchase::with('purchaseItems')->findOrFail($request->purchase_id);

            if ($purchase->status === 'received') {
                throw new \Exception('This purchase order is already fully received.');
            }

            if ($purchase->status === 'cancelled') {
                throw new \Exception('Cannot receive a cancelled purchase order.');
            }

            foreach ($request->items as $receiveItem) {
                $poItem = PurchaseItem::lockForUpdate()->findOrFail($receiveItem['purchase_item_id']);

                // Validate belongs to this PO
                if ($poItem->purchase_id !== $purchase->id) {
                    throw new \Exception('Item does not belong to this purchase order.');
                }

                $maxReceivable = $poItem->quantity_ordered - $poItem->quantity_received;

                if ($receiveItem['qty'] > $maxReceivable) {
                    throw new \Exception("Cannot receive more than ordered for item {$poItem->id}. Max: {$maxReceivable}");
                }

                // ── Add stock to variant ────────────────
                $variant   = ProductVariant::lockForUpdate()->findOrFail($poItem->variant_id);
                $prevStock = $variant->stock_quantity;
                $newStock  = $prevStock + $receiveItem['qty'];

                $variant->update([
                    'stock_quantity' => $newStock,
                    // Update expiry if provided in PO item
                    'expiry_date'    => $poItem->expiry_date ?? $variant->expiry_date,
                    'batch_number'   => $poItem->batch_number ?? $variant->batch_number,
                ]);

                // ── Log inventory adjustment ────────────
                InventoryAdjustment::create([
                    'variant_id'      => $variant->id,
                    'adjustment_type' => 'increase',
                    'quantity'        => $receiveItem['qty'],
                    'reason'          => "Received from PO {$purchase->local_id}",
                    'reference_type'  => Purchase::class,
                    'reference_id'    => $purchase->id,
                    'adjusted_by'     => auth()->id(),
                    'previous_stock'  => $prevStock,
                    'new_stock'       => $newStock,
                ]);

                // ── Update purchase item received qty ───
                $poItem->update([
                    'quantity_received' => $poItem->quantity_received + $receiveItem['qty'],
                ]);
            }

            // ── Update PO status ────────────────────────
            $purchase->refresh();
            $allItems    = $purchase->purchaseItems()->get();
            $allReceived = $allItems->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
            $anyReceived = $allItems->some(fn($i) => $i->quantity_received > 0);

            $purchase->update([
                'status'      => $allReceived ? 'received' : ($anyReceived ? 'partial' : 'ordered'),
                'received_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'new_status'   => $purchase->status,
                'all_received' => $allReceived,
                'message'      => $allReceived
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
}

