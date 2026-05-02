<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventoryController extends Controller
{
    // ══════════════════════════════════════════════
    //  PAGE LOAD — returns the blade view with
    //  all sidebar stats pre-loaded
    // ══════════════════════════════════════════════
    public function page()
    {
        $totalProducts   = ProductVariant::count();
        $activeProducts  = ProductVariant::where('is_active', true)->count();

        $lowStockCount   = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereRaw('product_variants.stock_quantity <= COALESCE(products.low_stock_threshold, 10)')
            ->where('product_variants.is_active', true)
            ->count();

        $expiringSoon    = ProductVariant::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays(30))
            ->count();

        $inventoryValue = ProductVariant::selectRaw('SUM(stock_quantity * COALESCE(cost_price, 0)) as total')
            ->value('total') ?? 0;

        $categoryCount   = Category::count();
        $categories      = Category::orderBy('name')->get();
        $suppliers       = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.inventory', compact(
            'totalProducts',
            'activeProducts',
            'lowStockCount',
            'expiringSoon',
            'inventoryValue',
            'categoryCount',
            'categories',
            'suppliers'
        ));
    }

    // ══════════════════════════════════════════════
    //  INDEX — JSON paginated product list
    //  GET /pos/inventory/products
    // ══════════════════════════════════════════════
    public function index(Request $request)
    {
        $q        = $request->input('q', '');
        $category = $request->input('category');
        $supplier = $request->input('supplier');
        $stock    = $request->input('stock');
        $tab      = $request->input('tab', 'all');
        $sortCol  = $request->input('sort', 'name');
        $sortDir  = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        // Allowed sort columns mapped to real columns
        $sortMap = [
            'name'     => 'products.name',
            'sku'      => 'product_variants.sku',
            'category' => 'categories.name',
            'price'    => 'product_variants.price',
            'cost'     => 'product_variants.cost_price',
            'stock'    => 'product_variants.stock_quantity',
        ];
        $orderBy = $sortMap[$sortCol] ?? 'products.name';

        $query = ProductVariant::query()
            ->join('products',   'products.id',   '=', 'product_variants.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('purchase_items', 'purchase_items.variant_id', '=', 'product_variants.id')
            ->leftJoin('purchases', function ($join) {
                $join->on('purchases.id', '=', 'purchase_items.purchase_id')
                    ->where('purchases.status', '!=', 'cancelled');
            })
            ->select([
                'product_variants.id as variant_id',
                'products.name',
                'products.name_ps',
                'products.name_dr',
                'products.description',
                'products.unit',
                'products.category_id',
                'products.low_stock_threshold as threshold',
                'categories.name as category',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.stock_quantity',
                'product_variants.expiry_date',
                'product_variants.batch_number',
                'product_variants.is_active',
                DB::raw('product_variants.price as price'),
                DB::raw('product_variants.cost_price as cost_price'),
                DB::raw('DATEDIFF(product_variants.expiry_date, CURDATE()) as days_to_expiry'),
            ])
            ->groupBy(
                'product_variants.id',
                'products.name',
                'products.name_ps',
                'products.name_dr',
                'products.description',
                'products.unit',
                'products.category_id',
                'products.low_stock_threshold',
                'categories.name',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.stock_quantity',
                'product_variants.expiry_date',
                'product_variants.batch_number',
                'product_variants.is_active',
                'product_variants.price',
                'product_variants.cost_price'
            );

        // ── Search ──
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('products.name',             'like', "%{$q}%")
                    ->orWhere('products.name_ps',        'like', "%{$q}%")
                    ->orWhere('products.name_dr',        'like', "%{$q}%")
                    ->orWhere('product_variants.sku',    'like', "%{$q}%")
                    ->orWhere('product_variants.barcode', 'like', "%{$q}%");
            });
        }

        // ── Filters ──
        if ($category) {
            $query->where('products.category_id', $category);
        }

        if ($supplier) {
            $query->where('purchases.supplier_id', $supplier);
        }

        if ($stock === 'ok') {
            $query->whereRaw('product_variants.stock_quantity > COALESCE(products.low_stock_threshold, 10)');
        } elseif ($stock === 'low') {
            $query->whereRaw('product_variants.stock_quantity > 0')
                ->whereRaw('product_variants.stock_quantity <= COALESCE(products.low_stock_threshold, 10)');
        } elseif ($stock === 'zero') {
            $query->where('product_variants.stock_quantity', 0);
        }

        // ── Tab filters ──
        match ($tab) {
            'low_stock' => $query->whereRaw('product_variants.stock_quantity <= COALESCE(products.low_stock_threshold, 10)')
                ->where('product_variants.is_active', true),
            'expiring'  => $query->whereNotNull('product_variants.expiry_date')
                ->whereDate('product_variants.expiry_date', '>=', today())
                ->whereDate('product_variants.expiry_date', '<=', today()->addDays(30)),
            'inactive'  => $query->where('product_variants.is_active', false),
            default     => $query->where('product_variants.is_active', true),
        };

        $paginated = $query->orderBy($orderBy, $sortDir)->paginate(20);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // ══════════════════════════════════════════════
    //  STORE — create or update product + variant
    //  POST /pos/inventory/products/store
    // ══════════════════════════════════════════════
    public function store(Request $request)
    {
        $isUpdate = $request->filled('variant_id');

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'sku'     => 'required|string|unique:product_variants,sku,' . ($request->variant_id ?? 'NULL'),
            'barcode' => 'required|string|unique:product_variants,barcode,' . ($request->variant_id ?? 'NULL'),
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock_quantity'       => 'nullable|integer|min:0',
            'low_stock_threshold'  => 'nullable|integer|min:0',
            'expiry_date'          => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            if ($isUpdate) {
                // ── UPDATE ──────────────────────────────
                $variant = ProductVariant::findOrFail($request->variant_id);
                $product = Product::findOrFail($variant->product_id);

                $product->update([
                    'name'                => $request->name,
                    'name_ps'             => $request->name_ps,
                    'name_dr'             => $request->name_dr,
                    'description'         => $request->description,
                    'category_id'         => $request->category_id,
                    'low_stock_threshold' => $request->low_stock_threshold ?? 10,
                    'updated_by'          => auth()->id(),
                ]);

                $variant->update([
                    'sku'          => $request->sku,
                    'barcode'      => $request->barcode,
                    'price'        => $request->price,
                    'cost_price'   => $request->cost_price,
                    'expiry_date'  => $request->expiry_date,
                    'batch_number' => $request->batch_number,
                ]);
            } else {
                // ── CREATE ──────────────────────────────
                $product = Product::create([
                    'name'                => $request->name,
                    'name_ps'             => $request->name_ps,
                    'name_dr'             => $request->name_dr,
                    'description'         => $request->description,
                    'category_id'         => $request->category_id,
                    'unit'                => $request->unit ?? 'piece',
                    'low_stock_threshold' => $request->low_stock_threshold ?? 10,
                    'has_variants'        => false,
                    'is_active'           => true,
                    'created_by'          => auth()->id(),
                    'updated_by'          => auth()->id(),
                ]);

                ProductVariant::create([
                    'product_id'     => $product->id,
                    'sku'            => $request->sku,
                    'barcode'        => $request->barcode,
                    'price'          => $request->price,
                    'cost_price'     => $request->cost_price,
                    'stock_quantity' => $request->stock_quantity ?? 0,
                    'expiry_date'    => $request->expiry_date,
                    'batch_number'   => $request->batch_number,
                    'is_active'      => true,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ══════════════════════════════════════════════
    //  TOGGLE ACTIVE
    //  POST /pos/inventory/products/{id}/toggle
    // ══════════════════════════════════════════════
    public function toggle(ProductVariant $variant)
    {
        $variant->update(['is_active' => ! $variant->is_active]);

        // Also toggle parent product if all variants are inactive
        $product        = $variant->product;
        $anyActive      = $product->variants()->where('is_active', true)->exists();
        $product->update(['is_active' => $anyActive]);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════
    //  ADJUST STOCK
    //  POST /pos/inventory/adjust
    // ══════════════════════════════════════════════
    public function adjust(Request $request)
    {
        $request->validate([
            'variant_id'      => 'required|integer|exists:product_variants,id',
            'type'            => 'required|in:increase,decrease,correction,damage,expiry,return_to_supplier',
            'quantity'        => 'required_unless:type,correction|integer|min:0',
            'new_count'       => 'required_if:type,correction|integer|min:0',
            'reason'          => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $variant      = ProductVariant::lockForUpdate()->findOrFail($request->variant_id);
            $previousStock = $variant->stock_quantity;

            // Calculate new stock
            $newStock = match ($request->type) {
                'increase'           => $previousStock + $request->quantity,
                'decrease',
                'damage',
                'expiry',
                'return_to_supplier' => max(0, $previousStock - $request->quantity),
                'correction'         => $request->new_count,
            };

            // Quantity recorded in adjustment
            $adjQty = $request->type === 'correction'
                ? abs($newStock - $previousStock)
                : $request->quantity;

            // Update stock
            $variant->update(['stock_quantity' => $newStock]);

            // Log the adjustment
            InventoryAdjustment::create([
                'variant_id'      => $variant->id,
                'adjustment_type' => $request->type,
                'quantity'        => $adjQty,
                'reason'          => $request->reason,
                'adjusted_by'     => auth()->id(),
                'previous_stock'  => $previousStock,
                'new_stock'       => $newStock,
            ]);

            DB::commit();

            return response()->json([
                'success'   => true,
                'new_stock' => $newStock,
                'message'   => "Stock updated: {$previousStock} → {$newStock}",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ══════════════════════════════════════════════
    //  STORE PURCHASE ORDER
    //  POST /pos/inventory/purchase/store
    // ══════════════════════════════════════════════
    public function storePurchase(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|integer|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'total_cost'    => 'required|numeric|min:0',
            'items'         => 'required|array|min:1',
            'items.*.variant_id'        => 'required|integer|exists:product_variants,id',
            'items.*.quantity_ordered'  => 'required|integer|min:1',
            'items.*.unit_cost'         => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $localId = 'PO-' . strtoupper(Str::random(8));

            $purchase = Purchase::create([
                'local_id'         => $localId,
                'supplier_id'      => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'purchase_date'    => $request->purchase_date,
                'delivery_date'    => $request->delivery_date,
                'status'           => 'ordered',
                'total_cost'       => $request->total_cost,
                'amount_paid'      => 0,
                'payment_status'   => 'unpaid',
                'notes'            => $request->notes,
                'created_by'       => auth()->id(),
                'sync_status'      => 'pending',
            ]);

            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id'       => $purchase->id,
                    'variant_id'        => $item['variant_id'],
                    'quantity_ordered'  => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'line_total'        => $item['quantity_ordered'] * $item['unit_cost'],
                    'expiry_date'       => $item['expiry_date'] ?? null,
                    'batch_number'      => $item['batch_number'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'local_id' => $localId,
                'message'  => "Purchase order {$localId} created successfully.",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
