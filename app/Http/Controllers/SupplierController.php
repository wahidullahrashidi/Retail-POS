<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ProductVariant;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE — blade with stats
    // ══════════════════════════════════════════
    public function page()
    {
        $stats = [
            'total'           => Supplier::count(),
            'active'          => Supplier::where('is_active', true)->count(),
            'open_pos'        => Purchase::whereIn('status', ['ordered', 'partial'])->count(),
            'unpaid'          => Purchase::whereIn('payment_status', ['unpaid', 'partial'])
                                         ->selectRaw('SUM(total_cost - amount_paid) as bal')
                                         ->value('bal') ?? 0,
            'total_purchased' => Purchase::where('status', '!=', 'cancelled')->sum('total_cost'),
        ];

        $cities = Supplier::whereNotNull('city')
                          ->where('city', '!=', '')
                          ->distinct()->orderBy('city')->pluck('city');

        return view('inventory.suppliers', compact('stats', 'cities'));
    }

    // ══════════════════════════════════════════
    //  INDEX — paginated supplier JSON
    //  GET /pos/suppliers
    // ══════════════════════════════════════════
    public function index(Request $request)
    {
        $q      = $request->input('q', '');
        $status = $request->input('status', '');
        $city   = $request->input('city', '');
        $sort   = $request->input('sort', 'name');
        $dir    = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'name'            => 'suppliers.name',
            'total_purchases' => 'total_purchases',
        ];
        $orderBy = $sortMap[$sort] ?? 'suppliers.name';

        $query = Supplier::query()
            ->leftJoin(
                DB::raw('(SELECT supplier_id,
                                 SUM(total_cost) as total_purchases,
                                 SUM(total_cost - amount_paid) as unpaid,
                                 SUM(CASE WHEN status IN ("ordered","partial") THEN 1 ELSE 0 END) as open_pos
                          FROM purchases
                          WHERE status != "cancelled"
                          GROUP BY supplier_id) as pa'),
                'pa.supplier_id', '=', 'suppliers.id'
            )
            ->select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.contact_person',
                'suppliers.phone',
                'suppliers.phone_secondary',
                'suppliers.email',
                'suppliers.address',
                'suppliers.city',
                'suppliers.payment_terms',
                'suppliers.notes',
                'suppliers.is_active',
                'suppliers.created_at',
                DB::raw('COALESCE(pa.total_purchases, 0) as total_purchases'),
                DB::raw('COALESCE(pa.unpaid, 0) as unpaid'),
                DB::raw('COALESCE(pa.open_pos, 0) as open_pos'),
            ]);

        if ($q) {
            $query->where(fn($qb) =>
                $qb->where('suppliers.name',  'like', "%{$q}%")
                   ->orWhere('suppliers.phone', 'like', "%{$q}%")
                   ->orWhere('suppliers.city',  'like', "%{$q}%")
                   ->orWhere('suppliers.contact_person', 'like', "%{$q}%")
            );
        }

        if ($status === 'active')   $query->where('suppliers.is_active', true);
        if ($status === 'inactive') $query->where('suppliers.is_active', false);
        if ($city)                  $query->where('suppliers.city', $city);

        $paginated = $query->orderBy($orderBy, $dir)->paginate(20);

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

    // ══════════════════════════════════════════
    //  STORE — create or update supplier
    //  POST /pos/suppliers/store
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $isUpdate   = $request->filled('supplier_id');
        $supplierId = $request->input('supplier_id');

        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20|unique:suppliers,phone' . ($isUpdate ? ",{$supplierId}" : ''),
            'phone_secondary' => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'contact_person'  => 'nullable|string|max:255',
            'payment_terms'   => 'nullable|string|max:255',
            'notes'           => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
        ]);

        $fields = $request->only([
            'name','contact_person','phone','phone_secondary',
            'email','address','city','payment_terms','notes','is_active',
        ]);

        if ($isUpdate) {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->update($fields);
        } else {
            $fields['is_active'] = true;
            $supplier = Supplier::create($fields);
        }

        return response()->json([
            'success'  => true,
            'supplier' => $supplier,
        ]);
    }

    // ══════════════════════════════════════════
    //  TOGGLE ACTIVE
    //  POST /pos/suppliers/{supplier}/toggle
    // ══════════════════════════════════════════
    public function toggle(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        return response()->json(['success' => true, 'is_active' => $supplier->is_active]);
    }

    // ══════════════════════════════════════════
    //  SUPPLIER PURCHASES — for detail panel
    //  GET /pos/suppliers/{supplier}/purchases
    // ══════════════════════════════════════════
    public function supplierPurchases(Supplier $supplier)
    {
        $purchases = $supplier->purchases()
            ->orderByDesc('purchase_date')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'local_id'      => $p->local_id,
                'purchase_date' => Carbon::parse($p->purchase_date)->format('d M Y'),
                'total_cost'    => (float)$p->total_cost,
                'amount_paid'   => (float)$p->amount_paid,
                'status'        => $p->status,
                'payment_status'=> $p->payment_status,
            ]);

        return response()->json($purchases);
    }
}
// purchase controller
