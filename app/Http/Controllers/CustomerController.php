<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // ══════════════════════════════════════════════
    //  PAGE — blade with stats
    // ══════════════════════════════════════════════
    public function page()
    {
        $stats = [
            'total'            => Customer::count(),
            'active'           => Customer::where('is_active', true)->count(),
            'with_loans'       => Loan::where('status', 'active')
                                      ->distinct('customer_id')->count('customer_id'),
            'overdue'          => Loan::where('status', 'overdue')->count(),
            'total_outstanding'=> Loan::where('status', 'active')->sum('remaining_balance'),
            'lifetime_sales'   => Sale::whereNotNull('customer_id')
                                      ->where('status', 'completed')
                                      ->sum('total_amount'),
        ];

        $cities = Customer::whereNotNull('city')
                          ->where('city', '!=', '')
                          ->distinct()
                          ->orderBy('city')
                          ->pluck('city');

        return view('pos.customers', compact('stats', 'cities'));
    }

    // ══════════════════════════════════════════════
    //  INDEX — paginated JSON list
    //  GET /pos/customers
    // ══════════════════════════════════════════════
    public function index(Request $request)
    {
        $q       = $request->input('q', '');
        $loan    = $request->input('loan', '');
        $city    = $request->input('city', '');
        $tab     = $request->input('tab', 'all');
        $sortCol = $request->input('sort', 'name');
        $sortDir = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'name'          => 'customers.name',
            'phone'         => 'customers.phone',
            'loan_balance'  => 'loan_balance',
        ];
        $orderBy = $sortMap[$sortCol] ?? 'customers.name';

        $query = Customer::query()
            ->leftJoin(
                DB::raw('(SELECT customer_id,
                                 SUM(remaining_balance) as loan_balance,
                                 COUNT(*) as loan_count
                          FROM loans
                          WHERE status = "active"
                          GROUP BY customer_id) as loan_agg'),
                'loan_agg.customer_id', '=', 'customers.id'
            )
            ->leftJoin(
                DB::raw('(SELECT customer_id,
                                 SUM(total_amount) as total_purchases,
                                 COUNT(*) as sale_count,
                                 MAX(created_at) as last_sale_at
                          FROM sales
                          WHERE status = "completed"
                          GROUP BY customer_id) as sale_agg'),
                'sale_agg.customer_id', '=', 'customers.id'
            )
            ->select([
                'customers.id',
                'customers.name',
                'customers.phone',
                'customers.phone_secondary',
                'customers.address',
                'customers.city',
                'customers.notes',
                'customers.credit_limit',
                'customers.is_active',
                'customers.created_at',
                DB::raw('COALESCE(loan_agg.loan_balance, 0) as loan_balance'),
                DB::raw('COALESCE(loan_agg.loan_count, 0) as loan_count'),
                DB::raw('COALESCE(sale_agg.total_purchases, 0) as total_purchases'),
                DB::raw('COALESCE(sale_agg.sale_count, 0) as sale_count'),
                'sale_agg.last_sale_at',
            ]);

        // ── Search ──
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('customers.name',  'like', "%{$q}%")
                   ->orWhere('customers.phone', 'like', "%{$q}%")
                   ->orWhere('customers.city',  'like', "%{$q}%");
            });
        }

        // ── Loan filter ──
        if ($loan === 'has_loan') {
            $query->where('loan_agg.loan_balance', '>', 0);
        } elseif ($loan === 'overdue') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('loans')
                    ->whereColumn('loans.customer_id', 'customers.id')
                    ->where('loans.status', 'overdue');
            });
        } elseif ($loan === 'no_loan') {
            $query->whereNull('loan_agg.loan_balance')
                  ->orWhere('loan_agg.loan_balance', 0);
        }

        // ── City filter ──
        if ($city) {
            $query->where('customers.city', $city);
        }

        // ── Tab filter ──
        match ($tab) {
            'active'   => $query->where('customers.is_active', true),
            'inactive' => $query->where('customers.is_active', false),
            default    => null,
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
    //  STORE — create or update customer
    //  POST /pos/customers/store
    // ══════════════════════════════════════════════
    public function store(Request $request)
    {
        $isUpdate  = $request->filled('customer_id');
        $customerId = $request->input('customer_id');

        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:20|unique:customers,phone' . ($isUpdate ? ",{$customerId}" : ''),
            'phone_secondary'  => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:500',
            'city'             => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:1000',
            'credit_limit'     => 'nullable|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        if ($isUpdate) {
            $customer = Customer::findOrFail($customerId);
            $customer->update($request->only([
                'name','phone','phone_secondary','address','city','notes','credit_limit','is_active'
            ]));
        } else {
            $customer = Customer::create([
                'name'            => $request->name,
                'phone'           => $request->phone,
                'phone_secondary' => $request->phone_secondary,
                'address'         => $request->address,
                'city'            => $request->city,
                'notes'           => $request->notes,
                'credit_limit'    => $request->credit_limit,
                'is_active'       => true,
            ]);
        }

        return response()->json([
            'success'  => true,
            'customer' => [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'phone'        => $customer->phone,
                'city'         => $customer->city,
                'loan_balance' => 0,
                'is_active'    => $customer->is_active,
            ],
        ]);
    }

    // ══════════════════════════════════════════════
    //  DETAIL — customer detail + recent sales
    //  GET /pos/customers/{customer}/detail
    // ══════════════════════════════════════════════
    public function detail(Customer $customer)
    {
        $recentSales = Sale::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->select(['id', 'local_id', 'total_amount', 'payment_method', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'id'             => $s->id,
                'local_id'       => $s->local_id,
                'total_amount'   => $s->total_amount,
                'payment_method' => $s->payment_method,
                'created_at'     => $s->created_at->format('d M Y, h:i A'),
            ]);

        $loanBalance = Loan::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->sum('remaining_balance');

        $loanCount = Loan::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->count();

        $totalPurchases = Sale::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $saleCount = Sale::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count();

        $lastSale = Sale::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->max('created_at');

        return response()->json([
            'customer'     => array_merge($customer->toArray(), [
                'loan_balance'    => $loanBalance,
                'loan_count'      => $loanCount,
                'total_purchases' => $totalPurchases,
                'sale_count'      => $saleCount,
                'last_sale_at'    => $lastSale,
            ]),
            'recent_sales' => $recentSales,
        ]);
    }

    // ══════════════════════════════════════════════
    //  LOAN — active loan + payment history
    //  GET /pos/customers/{customer}/loan
    // ══════════════════════════════════════════════
    public function loan(Customer $customer)
    {
        $loan = Loan::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->first();

        $payments = $loan
            ? LoanPayment::where('loan_id', $loan->id)
                ->orderByDesc('created_at')
                ->get()
                ->map(fn($p) => [
                    'id'             => $p->id,
                    'amount'         => $p->amount,
                    'receipt_number' => $p->receipt_number,
                    'notes'          => $p->notes,
                    'created_at'     => $p->created_at->format('d M Y'),
                ])
            : [];

        return response()->json([
            'loan'     => $loan,
            'payments' => $payments,
        ]);
    }

    // ══════════════════════════════════════════════
    //  PAYMENT — record loan payment
    //  POST /pos/customers/payment
    // ══════════════════════════════════════════════
    public function payment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
            'amount'  => 'required|numeric|min:0.01',
            'notes'   => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $loan = Loan::lockForUpdate()->findOrFail($request->loan_id);

            if ($request->amount > $loan->remaining_balance) {
                throw new \Exception('Amount exceeds remaining balance.');
            }

            $newPaid      = $loan->amount_paid + $request->amount;
            $newRemaining = $loan->remaining_balance - $request->amount;
            $newStatus    = $newRemaining <= 0 ? 'paid' : 'active';

            $loan->update([
                'amount_paid'       => $newPaid,
                'remaining_balance' => max(0, $newRemaining),
                'status'            => $newStatus,
                'payment_count'     => $loan->payment_count + 1,
                'last_payment_at'   => now(),
            ]);

            LoanPayment::create([
                'loan_id'        => $loan->id,
                'amount'         => $request->amount,
                'received_by'    => auth()->id(),
                'notes'          => $request->notes,
                'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
            ]);

            DB::commit();

            return response()->json([
                'success'       => true,
                'new_remaining' => max(0, $newRemaining),
                'status'        => $newStatus,
                'message'       => $newStatus === 'paid' ? 'Loan fully paid!' : 'Payment recorded.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ══════════════════════════════════════════════
    //  TOGGLE ACTIVE
    //  POST /pos/customers/{customer}/toggle
    // ══════════════════════════════════════════════
    public function toggle(Customer $customer)
    {
        $customer->update(['is_active' => ! $customer->is_active]);
        return response()->json(['success' => true, 'is_active' => $customer->is_active]);
    }

    // ══════════════════════════════════════════════
    //  SEARCH — for checkout / POS
    //  GET /pos/customers/search
    // ══════════════════════════════════════════════
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (empty($q)) return response()->json([]);

        return response()->json(
            Customer::query()
                ->where('is_active', true)
                ->where(fn($qb) =>
                    $qb->where('name',  'like', "%{$q}%")
                       ->orWhere('phone', 'like', "%{$q}%")
                       ->orWhere('city',  'like', "%{$q}%")
                )
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
                ])
        );
    }

    // ══════════════════════════════════════════════
    //  EXPORT CSV
    //  GET /pos/customers/export
    // ══════════════════════════════════════════════
    public function export(Request $request)
    {
        $q    = $request->input('q', '');
        $loan = $request->input('loan', '');
        $tab  = $request->input('tab', 'all');

        $customers = Customer::query()
            ->leftJoin(
                DB::raw('(SELECT customer_id, SUM(remaining_balance) as loan_balance FROM loans WHERE status="active" GROUP BY customer_id) as la'),
                'la.customer_id', '=', 'customers.id'
            )
            ->select([
                'customers.name', 'customers.phone', 'customers.phone_secondary',
                'customers.city', 'customers.address',
                DB::raw('COALESCE(la.loan_balance, 0) as loan_balance'),
                'customers.credit_limit', 'customers.is_active', 'customers.created_at',
            ])
            ->when($q, fn($qb) =>
                $qb->where('customers.name', 'like', "%{$q}%")
                   ->orWhere('customers.phone', 'like', "%{$q}%")
            )
            ->when($tab === 'active',   fn($qb) => $qb->where('customers.is_active', true))
            ->when($tab === 'inactive', fn($qb) => $qb->where('customers.is_active', false))
            ->orderBy('customers.name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Phone', 'Phone 2', 'City', 'Address', 'Loan Balance', 'Credit Limit', 'Active', 'Joined']);
            foreach ($customers as $c) {
                fputcsv($handle, [
                    $c->name, $c->phone, $c->phone_secondary,
                    $c->city, $c->address, $c->loan_balance,
                    $c->credit_limit, $c->is_active ? 'Yes' : 'No',
                    $c->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone|max:20',
            'city'  => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'name'      => trim($request->name),
            'phone'     => trim($request->phone),
            'city'      => $request->city,
            'notes'     => $request->notes,
            'is_active' => true,
        ]);

        return response()->json([
            'success'  => true,
            'customer' => [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'phone'        => $customer->phone,
                'city'         => $customer->city,
                'loan_balance' => 0,
            ],
        ]);
    }
}