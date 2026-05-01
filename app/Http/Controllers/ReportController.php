<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Shift;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE
    // ══════════════════════════════════════════
    public function page()
    {
        return view('reports.reports');
    }

    // ══════════════════════════════════════════
    //  MAIN DATA ENDPOINT
    //  GET /pos/reports/data?from=&to=&granularity=
    // ══════════════════════════════════════════
    public function data(Request $request)
    {
        $from        = Carbon::parse($request->input('from', today()))->startOfDay();
        $to          = Carbon::parse($request->input('to',   today()))->endOfDay();
        $granularity = $request->input('granularity', 'daily');

        // Previous period for trend comparison
        $diff     = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays($diff);
        $prevTo   = $from->copy()->subSecond();

        $completedSales = fn($q) => $q->whereBetween('created_at', [$from, $to])
            ->where('status', 'completed');
        $prevSales = fn($q)      => $q->whereBetween('created_at', [$prevFrom, $prevTo])
            ->where('status', 'completed');

        // ── Core KPIs ──────────────────────────
        $totalRevenue = Sale::where($completedSales)->sum('total_amount');
        $prevRevenue  = Sale::where($prevSales)->sum('total_amount');

        $totalCost    = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->sum(DB::raw('sale_items.quantity * COALESCE(sale_items.cost_price, 0)'));

        $netProfit      = $totalRevenue - $totalCost;
        $margin         = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $revenueTrend   = $prevRevenue  > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $totalTx        = Sale::where($completedSales)->count();
        $itemsSold      = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->sum('sale_items.quantity');

        $avgTransaction = $totalTx > 0 ? $totalRevenue / $totalTx : 0;

        $cashSales      = Sale::where($completedSales)->where('payment_method', 'cash')->sum('total_amount');
        $loanSales      = Sale::where($completedSales)->where('payment_method', 'loan')->sum('total_amount');
        $totalDiscounts = Sale::where($completedSales)->sum('discount_amount');
        $discountRate   = $totalRevenue > 0 ? ($totalDiscounts / ($totalRevenue + $totalDiscounts)) * 100 : 0;

        $returnCount    = Sale::whereBetween('created_at', [$from, $to])->where('sale_type', 'return')->count();
        $returnAmount   = Sale::whereBetween('created_at', [$from, $to])->where('sale_type', 'return')->sum('total_amount');

        // ── Trend labels & series ──────────────
        [$trendLabels, $trendRevenue, $trendProfit, $dailyCash, $dailyLoan, $dailyLabels] =
            $this->buildTrendSeries($from, $to, $granularity);

        // ── Hourly heatmap ─────────────────────
        $hourlyRaw = Sale::where($completedSales)
            ->select(DB::raw('HOUR(created_at) as hr'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('hr')->get()->keyBy('hr');
        $hourlyHeatmap = collect(range(0, 23))->map(fn($h) => (float)($hourlyRaw[$h]->total ?? 0))->values()->toArray();
        $hourlyMax     = max($hourlyHeatmap) ?: 1;

        // ── Avg by day of week ─────────────────
        $weekdayRaw = Sale::where($completedSales)
            ->select(DB::raw('DAYOFWEEK(created_at) as dow'), DB::raw('AVG(total_amount) as avg_sale'))
            ->groupBy('dow')->get()->keyBy('dow');
        $weekdayAvg = collect(range(1, 7))->map(fn($d) => round($weekdayRaw[$d]->avg_sale ?? 0, 2))->values()->toArray();

        // ── Top categories ─────────────────────
        $catRevenue = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->select('categories.name', DB::raw('SUM(sale_items.line_total) as revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(6)->get();

        $catTotal = $catRevenue->sum('revenue') ?: 1;
        $topCategories = $catRevenue->map(fn($c) => [
            'name'    => $c->name,
            'revenue' => (float)$c->revenue,
            'pct'     => round(($c->revenue / $catTotal) * 100, 1),
        ])->values();

        // ── Top products ───────────────────────
        $topProducts = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->select([
                'products.name',
                'product_variants.sku',
                DB::raw('SUM(sale_items.quantity) as qty_sold'),
                DB::raw('SUM(sale_items.line_total) as revenue'),
                DB::raw('SUM(sale_items.quantity * COALESCE(sale_items.cost_price,0)) as cost'),
            ])
            ->groupBy('product_variants.id', 'products.name', 'product_variants.sku')
            ->orderByDesc('revenue')
            ->limit(10)->get()
            ->map(fn($p) => [
                'name'    => $p->name,
                'sku'     => $p->sku,
                'qty_sold' => (int)$p->qty_sold,
                'revenue' => (float)$p->revenue,
                'profit'  => (float)($p->revenue - $p->cost),
            ])->values();

        // ── Slow products ──────────────────────
        $slowProducts = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.status', 'completed')
            ->where('product_variants.is_active', true)
            ->select([
                'products.name',
                'product_variants.sku',
                'product_variants.stock_quantity as stock',
                DB::raw('SUM(sale_items.quantity) as qty_sold'),
                DB::raw('SUM(sale_items.line_total) as revenue'),
            ])
            ->groupBy('product_variants.id', 'products.name', 'product_variants.sku', 'product_variants.stock_quantity')
            ->orderBy('qty_sold')
            ->limit(10)->get()
            ->map(fn($p) => [
                'name'    => $p->name,
                'sku'     => $p->sku,
                'qty_sold' => (int)$p->qty_sold,
                'stock'   => (int)$p->stock,
                'revenue' => (float)$p->revenue,
            ])->values();

        // ── Margin table ───────────────────────
        $marginTable = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.is_active', true)
            ->whereNotNull('product_variants.cost_price')
            ->where('product_variants.cost_price', '>', 0)
            ->select([
                'products.name',
                'product_variants.sku',
                DB::raw('COALESCE(product_variants.price, products.base_price) as price'),
                'product_variants.cost_price as cost',
            ])
            ->limit(20)->get()
            ->map(fn($p) => [
                'name'       => $p->name,
                'sku'        => $p->sku,
                'price'      => (float)$p->price,
                'cost'       => (float)$p->cost,
                'margin'     => $p->price > 0 ? round((($p->price - $p->cost) / $p->price) * 100, 1) : 0,
                'profit_unit' => round($p->price - $p->cost, 2),
            ])
            ->sortByDesc('margin')->values();

        // ── Top sales ──────────────────────────
        $topSales = Sale::where($completedSales)
            ->with('customer:id,name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'id'       => $s->id,
                'local_id' => $s->local_id,
                'customer' => $s->customer?->name,
                'method'   => $s->payment_method,
                'total'    => (float)$s->total_amount,
            ])->values();

        // ── Average daily sales ────────────────
        $dayCount      = max($from->diffInDays($to) + 1, 1);
        $avgDailySales = $totalRevenue / $dayCount;

        // ── Inventory ──────────────────────────
        $stockZero  = ProductVariant::where('is_active', true)->where('stock_quantity', 0)->count();
        $stockLow   = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.is_active', true)
            ->where('product_variants.stock_quantity', '>', 0)
            ->whereRaw('product_variants.stock_quantity <= COALESCE(products.low_stock_threshold,10)')
            ->count();
        $stockOk    = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.is_active', true)
            ->whereRaw('product_variants.stock_quantity > COALESCE(products.low_stock_threshold,10)')
            ->count();
        $expiring30 = ProductVariant::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays(30))
            ->count();

        $invValueCost = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->selectRaw('SUM(product_variants.stock_quantity * COALESCE(product_variants.cost_price,0)) as val')
            ->value('val') ?? 0;

        $invValueRetail = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->selectRaw('SUM(product_variants.stock_quantity * COALESCE(product_variants.price, products.base_price, 0)) as val')
            ->value('val') ?? 0;

        $criticalStock = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('product_variants.is_active', true)
            ->where(fn($q) => $q->where('product_variants.stock_quantity', 0)
                ->orWhereRaw('product_variants.stock_quantity <= COALESCE(products.low_stock_threshold,10)'))
            ->select([
                'products.name',
                'product_variants.sku',
                'categories.name as category',
                'product_variants.stock_quantity as stock',
                'products.low_stock_threshold as threshold',
                'product_variants.expiry_date',
                DB::raw('product_variants.stock_quantity * COALESCE(product_variants.cost_price,0) as cost_value'),
                DB::raw('DATEDIFF(product_variants.expiry_date, CURDATE()) as expiry_days'),
            ])
            ->orderBy('product_variants.stock_quantity')
            ->limit(15)->get()
            ->map(fn($p) => [
                'name'       => $p->name,
                'sku'        => $p->sku,
                'category'   => $p->category,
                'stock'      => (int)$p->stock,
                'threshold'  => (int)($p->threshold ?? 10),
                'cost_value' => (float)$p->cost_value,
                'expiry'     => $p->expiry_date?->format('d M Y'),
                'expiry_days' => (int)($p->expiry_days ?? 9999),
            ])->values();

        $invByCategory = SaleItem::join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('SUM(product_variants.stock_quantity * COALESCE(product_variants.cost_price,0)) as value'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('value')
            ->limit(8)->get()
            ->map(fn($c) => ['name' => $c->name, 'value' => (float)$c->value])->values();

        // ── Cashiers ───────────────────────────
        $cashierData = Shift::join('users', 'users.id', '=', 'shifts.user_id')
            ->leftJoin('sales', 'sales.shift_id', '=', 'shifts.id')
            ->whereBetween('shifts.opened_at', [$from, $to])
            ->select([
                'shifts.user_id as id',
                'users.name',
                DB::raw('COUNT(DISTINCT shifts.id) as shift_count'),
                DB::raw('COUNT(sales.id) as tx_count'),
                DB::raw('SUM(CASE WHEN sales.status="completed" THEN sales.total_amount ELSE 0 END) as total_sales'),
            ])
            ->groupBy('shifts.user_id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        $maxSales = $cashierData->max('total_sales') ?: 1;
        $cashiers = $cashierData->map(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'shift_count' => (int)$c->shift_count,
            'tx_count'    => (int)$c->tx_count,
            'total_sales' => (float)$c->total_sales,
            'avg_ticket'  => $c->tx_count > 0 ? round($c->total_sales / $c->tx_count, 0) : 0,
            'pct'         => round(($c->total_sales / $maxSales) * 100, 1),
        ])->values();

        $shifts = Shift::join('users', 'users.id', '=', 'shifts.user_id')
            ->select([
                'shifts.id',
                'users.name as cashier',
                'shifts.opened_at',
                'shifts.closed_at',
                'shifts.starting_cash',
                'shifts.expected_cash',
                'shifts.actual_cash',
                'shifts.discrepancy',
                'shifts.discrepancy_note',
                'shifts.is_closed',
            ])
            ->whereBetween('shifts.opened_at', [$from, $to])
            ->orderByDesc('shifts.opened_at')
            ->limit(20)->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'cashier'          => $s->cashier,
                'opened_at'        => Carbon::parse($s->opened_at)->format('d M Y H:i'),
                'closed_at'        => $s->closed_at ? Carbon::parse($s->closed_at)->format('d M Y H:i') : null,
                'starting_cash'    => (float)$s->starting_cash,
                'expected_cash'    => $s->expected_cash ? (float)$s->expected_cash : null,
                'actual_cash'      => $s->actual_cash   ? (float)$s->actual_cash   : null,
                'discrepancy'      => $s->discrepancy   ? (float)$s->discrepancy   : null,
                'discrepancy_note' => $s->discrepancy_note,
                'is_closed'        => (bool)$s->is_closed,
            ])->values();

        // ── Loans ──────────────────────────────
        $loanOutstanding     = Loan::where('status', 'active')->sum('remaining_balance');
        $loanActiveCount     = Loan::where('status', 'active')->count();
        $loanOverdue         = Loan::where('status', 'overdue')->count();
        $loanOverdueAmount   = Loan::where('status', 'overdue')->sum('remaining_balance');
        $loanNewCount        = Loan::whereBetween('created_at', [$from, $to])->count();
        $loanNewAmount       = Loan::whereBetween('created_at', [$from, $to])->sum('original_amount');
        $loanCollected       = LoanPayment::whereBetween('created_at', [$from, $to])->sum('amount');
        $loanPaymentCount    = LoanPayment::whereBetween('created_at', [$from, $to])->count();

        // Aging buckets
        $now = now();
        $loanAging = [
            [
                'label' => 'Not yet due',
                'fill' => '#15803d',
                'color' => 'color:var(--green)',
                'amount' => Loan::where('status', 'active')->where('due_date', '>=', $now)->sum('remaining_balance'),
                'count' => Loan::where('status', 'active')->where('due_date', '>=', $now)->count()
            ],
            [
                'label' => '1–30 days overdue',
                'fill' => '#d97706',
                'color' => 'color:var(--amber)',
                'amount' => Loan::where('status', 'overdue')->whereBetween('due_date', [$now->copy()->subDays(30), $now])->sum('remaining_balance'),
                'count' => Loan::where('status', 'overdue')->whereBetween('due_date', [$now->copy()->subDays(30), $now])->count()
            ],
            [
                'label' => '31–90 days overdue',
                'fill' => '#dc2626',
                'color' => 'color:var(--red)',
                'amount' => Loan::where('status', 'overdue')->whereBetween('due_date', [$now->copy()->subDays(90), $now->copy()->subDays(31)])->sum('remaining_balance'),
                'count' => Loan::where('status', 'overdue')->whereBetween('due_date', [$now->copy()->subDays(90), $now->copy()->subDays(31)])->count()
            ],
            [
                'label' => '90+ days overdue',
                'fill' => '#7c3aed',
                'color' => 'color:var(--violet)',
                'amount' => Loan::where('status', 'overdue')->where('due_date', '<', $now->copy()->subDays(90))->sum('remaining_balance'),
                'count' => Loan::where('status', 'overdue')->where('due_date', '<', $now->copy()->subDays(90))->count()
            ],
        ];
        $agingTotal = collect($loanAging)->sum('amount') ?: 1;
        foreach ($loanAging as &$b) {
            $b['pct'] = round(($b['amount'] / $agingTotal) * 100, 1);
        }

        $overdueLoans = Loan::join('customers', 'customers.id', '=', 'loans.customer_id')
            ->where('loans.status', 'overdue')
            ->select(['loans.id', 'customers.name as customer', 'customers.phone', 'loans.original_amount', 'loans.amount_paid', 'loans.remaining_balance', 'loans.due_date'])
            ->orderBy('loans.due_date')
            ->limit(10)->get()
            ->map(fn($l) => [
                'id'          => $l->id,
                'customer'    => $l->customer,
                'phone'       => $l->phone,
                'original'    => (float)$l->original_amount,
                'paid'        => (float)$l->amount_paid,
                'remaining'   => (float)$l->remaining_balance,
                'due_date'    => Carbon::parse($l->due_date)->format('d M Y'),
                'days_overdue' => Carbon::parse($l->due_date)->diffInDays(today()),
            ])->values();

        // Loan issued vs collected series
        [$loanIssuedSeries, $loanCollectedSeries] = $this->buildLoanSeries($from, $to, $trendLabels);

        return response()->json([
            // KPIs
            'total_revenue'       => round($totalRevenue, 2),
            'net_profit'          => round($netProfit, 2),
            'margin'              => round($margin, 2),
            'revenue_trend'       => round($revenueTrend, 2),
            'total_transactions'  => $totalTx,
            'items_sold'          => (int)$itemsSold,
            'avg_transaction'     => round($avgTransaction, 2),
            'cash_sales'          => round($cashSales, 2),
            'loan_sales'          => round($loanSales, 2),
            'total_discounts'     => round($totalDiscounts, 2),
            'discount_rate'       => round($discountRate, 2),
            'return_count'        => $returnCount,
            'return_amount'       => round($returnAmount, 2),
            'avg_daily_sales'     => round($avgDailySales, 2),

            // Trend charts
            'trend_labels'        => $trendLabels,
            'trend_revenue'       => $trendRevenue,
            'trend_profit'        => $trendProfit,
            'daily_cash'          => $dailyCash,
            'daily_loan'          => $dailyLoan,
            'daily_labels'        => $dailyLabels,
            'hourly_heatmap'      => $hourlyHeatmap,
            'hourly_max'          => $hourlyMax,
            'weekday_avg'         => $weekdayAvg,

            // Products
            'top_categories'      => $topCategories,
            'top_products'        => $topProducts,
            'slow_products'       => $slowProducts,
            'margin_table'        => $marginTable,
            'top_sales'           => $topSales,

            // Inventory
            'stock_zero'          => $stockZero,
            'stock_low'           => $stockLow,
            'stock_ok'            => $stockOk,
            'expiring_30'         => $expiring30,
            'inv_value_cost'      => round($invValueCost, 2),
            'inv_value_retail'    => round($invValueRetail, 2),
            'critical_stock'      => $criticalStock,
            'inv_by_category'     => $invByCategory,

            // Cashiers
            'cashiers'            => $cashiers,
            'shifts'              => $shifts,

            // Loans
            'loan_outstanding'    => round($loanOutstanding, 2),
            'loan_active_count'   => $loanActiveCount,
            'loan_overdue'        => $loanOverdue,
            'loan_overdue_amount' => round($loanOverdueAmount, 2),
            'loan_new_count'      => $loanNewCount,
            'loan_new_amount'     => round($loanNewAmount, 2),
            'loan_collected'      => round($loanCollected, 2),
            'loan_payment_count'  => $loanPaymentCount,
            'loan_aging'          => $loanAging,
            'overdue_loans'       => $overdueLoans,
            'loan_issued_series'  => $loanIssuedSeries,
            'loan_collected_series' => $loanCollectedSeries,
        ]);
    }

    // ══════════════════════════════════════════
    //  Z-REPORT
    //  GET /pos/reports/zreport?shift_id=
    // ══════════════════════════════════════════
    public function zreport(Request $request)
    {
        $shift = Shift::with('user')->findOrFail($request->input('shift_id'));

        $shiftSales = Sale::where('shift_id', $shift->id)->where('status', 'completed');
        $totalSales = $shiftSales->sum('total_amount');
        $cashSales  = Sale::where('shift_id', $shift->id)->where('status', 'completed')->where('payment_method', 'cash')->sum('total_amount');
        $loanSales  = Sale::where('shift_id', $shift->id)->where('status', 'completed')->where('payment_method', 'loan')->sum('total_amount');
        $discounts  = Sale::where('shift_id', $shift->id)->where('status', 'completed')->sum('discount_amount');
        $returns    = Sale::where('shift_id', $shift->id)->where('sale_type', 'return')->sum('total_amount');
        $txCount    = Sale::where('shift_id', $shift->id)->where('status', 'completed')->count();
        $itemsSold  = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.shift_id', $shift->id)->where('sales.status', 'completed')
            ->sum('sale_items.quantity');

        return response()->json([
            'cashier'         => $shift->user->name,
            'shift_date'      => Carbon::parse($shift->opened_at)->format('d M Y, H:i') .
                ($shift->closed_at ? ' → ' . Carbon::parse($shift->closed_at)->format('H:i') : ' (Active)'),
            'total_sales'     => (float)$totalSales,
            'cash_sales'      => (float)$cashSales,
            'loan_sales'      => (float)$loanSales,
            'discounts'       => (float)$discounts,
            'returns'         => (float)$returns,
            'tx_count'        => $txCount,
            'items_sold'      => (int)$itemsSold,
            'avg_ticket'      => $txCount > 0 ? round($totalSales / $txCount, 0) : 0,
            'starting_cash'   => (float)$shift->starting_cash,
            'expected_cash'   => $shift->expected_cash ? (float)$shift->expected_cash : (float)($shift->starting_cash + $cashSales - $returns),
            'actual_cash'     => $shift->actual_cash     ? (float)$shift->actual_cash     : null,
            'discrepancy'     => $shift->discrepancy     ? (float)$shift->discrepancy     : null,
            'discrepancy_note' => $shift->discrepancy_note,
        ]);
    }

    // ══════════════════════════════════════════
    //  EXPORT
    //  GET /pos/reports/export?from=&to=&type=
    // ══════════════════════════════════════════
    public function export(Request $request)
    {
        $from = Carbon::parse($request->input('from', today()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   today()))->endOfDay();
        $type = $request->input('type', 'csv');

        $sales = Sale::with('customer:id,name')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        $filename = "sales-{$from->format('Y-m-d')}-to-{$to->format('Y-m-d')}.csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Sale ID', 'Customer', 'Date', 'Payment Method', 'Subtotal', 'Discount', 'Total', 'Status']);
            foreach ($sales as $s) {
                fputcsv($handle, [
                    $s->local_id,
                    $s->customer?->name ?? 'Walk-in',
                    $s->created_at->format('Y-m-d H:i'),
                    $s->payment_method,
                    $s->subtotal,
                    $s->discount_amount,
                    $s->total_amount,
                    $s->status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════
    private function buildTrendSeries(Carbon $from, Carbon $to, string $granularity): array
    {
        $labels = $revenue = $profit = $cash = $loan = $dailyLabels = [];

        $days = $from->diffInDays($to) + 1;

        if ($granularity === 'hourly' && $days <= 2) {
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $row = Sale::whereBetween('created_at', [$from->copy()->addHours($h), $from->copy()->addHours($h + 1)->subSecond()])
                    ->where('status', 'completed')
                    ->selectRaw('SUM(total_amount) as rev, SUM(discount_amount) as disc, payment_method')
                    ->first();
                $rev = (float)($row->rev ?? 0);
                $revenue[] = round($rev, 2);
                $profit[]  = round($rev * 0.3, 2); // placeholder; replace with real cost join if needed
                $cash[]    = 0;
                $loan[] = 0;
            }
        } elseif ($granularity === 'monthly' || $days > 90) {
            $cursor = $from->copy()->startOfMonth();
            while ($cursor->lte($to)) {
                $start = $cursor->copy()->startOfMonth();
                $end   = $cursor->copy()->endOfMonth();
                $labels[] = $cursor->format('M Y');
                $this->appendDayRow($start, $end, $revenue, $profit, $cash, $loan);
                $cursor->addMonth();
            }
        } elseif ($granularity === 'weekly' || $days > 30) {
            $cursor = $from->copy()->startOfWeek();
            while ($cursor->lte($to)) {
                $end = $cursor->copy()->endOfWeek();
                $labels[] = $cursor->format('d M');
                $this->appendDayRow($cursor, $end, $revenue, $profit, $cash, $loan);
                $cursor->addWeek();
            }
        } else {
            $cursor = $from->copy();
            while ($cursor->lte($to)) {
                $labels[] = $cursor->format('d M');
                $start = $cursor->copy()->startOfDay();
                $end   = $cursor->copy()->endOfDay();
                $this->appendDayRow($start, $end, $revenue, $profit, $cash, $loan);
                $cursor->addDay();
            }
        }

        return [$labels, $revenue, $profit, $cash, $loan, $labels];
    }

    private function appendDayRow(Carbon $start, Carbon $end, &$revenue, &$profit, &$cash, &$loan): void
    {
        $rows = Sale::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->selectRaw('SUM(total_amount) as rev, SUM(discount_amount) as disc, payment_method')
            ->groupBy('payment_method')->get()->keyBy('payment_method');

        $cashVal  = (float)($rows['cash']->rev ?? 0);
        $loanVal  = (float)($rows['loan']->rev ?? 0);
        $rev      = $cashVal + $loanVal;

        $costVal  = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sale_items.quantity * COALESCE(sale_items.cost_price,0)'));

        $revenue[] = round($rev, 2);
        $profit[]  = round($rev - $costVal, 2);
        $cash[]    = round($cashVal, 2);
        $loan[]    = round($loanVal, 2);
    }

    private function buildLoanSeries(Carbon $from, Carbon $to, array $labels): array
    {
        $issued    = [];
        $collected = [];
        $cursor    = $from->copy();

        foreach ($labels as $_) {
            $end = $cursor->copy()->endOfDay();
            $issued[]    = round(Loan::whereBetween('created_at', [$cursor->copy()->startOfDay(), $end])->sum('original_amount'), 2);
            $collected[] = round(LoanPayment::whereBetween('created_at', [$cursor->copy()->startOfDay(), $end])->sum('amount'), 2);
            $cursor->addDay();
            if ($cursor->gt($to)) break;
        }

        return [$issued, $collected];
    }
}
