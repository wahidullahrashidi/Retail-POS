<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // ══════════════════════════════════════════
    //  OPEN SHIFT FORM
    //  GET /shift/open
    // ══════════════════════════════════════════
    public function openForm()
    {
        $existing = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->first();

        if ($existing) {
            return redirect()->route('pos.dashboard');
        }

        // Pass hijri date to view
        $hijriDate = $this->getHijriDate();

        return view('shifts.open', compact('hijriDate'));
    }

    // ══════════════════════════════════════════
    //  OPEN SHIFT (submit)
    //  POST /shift/open
    // ══════════════════════════════════════════
    public function open(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $existing = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->first();

        if ($existing) {
            return redirect()->route('pos.dashboard');
        }

        Shift::create([
            'user_id'       => Auth::id(),
            'opened_at'     => now(),
            'starting_cash' => $request->starting_cash,
            'is_closed'     => false,
        ]);

        return redirect()->route('pos.dashboard')
            ->with('success', 'Shift opened successfully. Good selling!');
    }

    // ══════════════════════════════════════════
    //  CLOSE SHIFT FORM
    //  GET /shift/close
    // ══════════════════════════════════════════
    public function closeForm()
{
    $shift = Shift::where('user_id', Auth::id())
        ->where('is_closed', false)
        ->firstOrFail();

    // One query instead of three
    $summary = Sale::where('shift_id', $shift->id)
        ->whereIn('status', ['completed', 'refunded'])
        ->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_sales,
            COALESCE(SUM(CASE WHEN status = 'refunded' THEN total_amount ELSE 0 END), 0) as cash_refunds,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as tx_count
        ")
        ->first();

    $cashSales      = (float) $summary->cash_sales;
    $cashRefunds    = (float) $summary->cash_refunds;
    $transactionCount = (int) $summary->tx_count;

    $expectedCash = $shift->starting_cash + $cashSales - $cashRefunds;

    return view('shifts.close', compact(
        'shift', 'expectedCash', 'cashSales', 'cashRefunds', 'transactionCount'
    ));
}

    // ══════════════════════════════════════════
    //  CLOSE SHIFT (submit)
    //  POST /shift/close
    // ══════════════════════════════════════════
    public function close(Request $request)
{
    $request->validate([
        'actual_cash'      => 'required|numeric|min:0',
        'discrepancy_note' => 'nullable|string|max:500',
    ]);

    $shift = Shift::where('user_id', Auth::id())
        ->where('is_closed', false)
        ->firstOrFail();

    $summary = Sale::where('shift_id', $shift->id)
        ->whereIn('status', ['completed', 'refunded'])
        ->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_sales,
            COALESCE(SUM(CASE WHEN status = 'refunded' THEN total_amount ELSE 0 END), 0) as cash_refunds
        ")
        ->first();

    $expectedCash = $shift->starting_cash + $summary->cash_sales - $summary->cash_refunds;
    $actualCash   = $request->actual_cash;
    $discrepancy  = $actualCash - $expectedCash;

    $shift->update([
        'closed_at'        => now(),
        'expected_cash'    => $expectedCash,
        'actual_cash'      => $actualCash,
        'discrepancy'      => $discrepancy,
        'discrepancy_note' => $request->discrepancy_note,
        'is_closed'        => true,
        'closed_by'        => Auth::id(),
    ]);

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('success', 'Shift closed successfully. Please log in for your next shift.');
}

    // ══════════════════════════════════════════
    //  SHIFTS PAGE — blade with stats
    //  GET /pos/shifts/page
    // ══════════════════════════════════════════
    public function page()
    {
        $stats = [
            'active'       => Shift::where('is_closed', false)->count(),
            'today'        => Shift::whereDate('opened_at', today())->count(),
            'discrepancies'=> Shift::where('is_closed', true)
                                   ->whereNotNull('discrepancy')
                                   ->where('discrepancy', '!=', 0)
                                   ->count(),
            'avg_duration' => $this->getAvgDuration(),
        ];

        $cashiers = User::whereHas('shifts')->orderBy('name')->get(['id','name']);

        return view('shifts.shiftIndex', compact('stats', 'cashiers'));
    }

    // ══════════════════════════════════════════
    //  INDEX — paginated JSON
    //  GET /pos/shifts
    // ══════════════════════════════════════════
    public function index(Request $request)
    {
        $q    = $request->input('q', '');
        $from = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to   = $request->input('to')   ? Carbon::parse($request->input('to'))->endOfDay()     : null;
        $user = $request->input('user', '');
        $tab  = $request->input('tab', 'all');

        $query = Shift::query()
            ->join('users', 'users.id', '=', 'shifts.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('users as closer', 'closer.id', '=', 'shifts.closed_by')
            ->leftJoin(
                DB::raw('(SELECT shift_id,
                                 SUM(CASE WHEN payment_method="cash" AND status="completed" THEN total_amount ELSE 0 END) as cash_sales,
                                 COUNT(CASE WHEN status="completed" THEN 1 END) as tx_count
                          FROM sales GROUP BY shift_id) as sa'),
                'sa.shift_id', '=', 'shifts.id'
            )
            ->select([
                'shifts.id',
                'shifts.opened_at',
                'shifts.closed_at',
                'shifts.starting_cash',
                'shifts.expected_cash',
                'shifts.actual_cash',
                'shifts.discrepancy',
                'shifts.discrepancy_note',
                'shifts.is_closed',
                'users.name as cashier',
                'roles.display_name as role',
                'closer.name as closed_by',
                DB::raw('COALESCE(sa.cash_sales, 0) as cash_sales'),
                DB::raw('COALESCE(sa.tx_count, 0) as tx_count'),
            ]);

        if ($q)    $query->where('users.name', 'like', "%{$q}%");
        if ($from) $query->where('shifts.opened_at', '>=', $from);
        if ($to)   $query->where('shifts.opened_at', '<=', $to);
        if ($user) $query->where('shifts.user_id', $user);

        match ($tab) {
            'active' => $query->where('shifts.is_closed', false),
            'closed' => $query->where('shifts.is_closed', true),
            default  => null,
        };

        $paginated = $query->orderByDesc('shifts.opened_at')->paginate(20);

        $items = collect($paginated->items())->map(fn($s) => [
            'id'               => $s->id,
            'cashier'          => $s->cashier,
            'role'             => $s->role,
            'closed_by'        => $s->closed_by,
            'opened_at'        => Carbon::parse($s->opened_at)->format('d M Y, H:i'),
            'closed_at'        => $s->closed_at ? Carbon::parse($s->closed_at)->format('d M Y, H:i') : null,
            'duration'         => $s->closed_at
                ? $this->formatDuration($s->opened_at, $s->closed_at)
                : null,
            'starting_cash'    => (float)$s->starting_cash,
            'expected_cash'    => (float)($s->expected_cash ?? 0),
            'actual_cash'      => $s->actual_cash !== null ? (float)$s->actual_cash : null,
            'discrepancy'      => $s->discrepancy !== null ? (float)$s->discrepancy : null,
            'discrepancy_note' => $s->discrepancy_note,
            'is_closed'        => (bool)$s->is_closed,
            'cash_sales'       => (float)$s->cash_sales,
            'tx_count'         => (int)$s->tx_count,
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
    //  DETAIL — shift detail + top items
    //  GET /pos/shifts/{shift}/detail
    // ══════════════════════════════════════════
    public function detail(Shift $shift)
{
    $summary = Sale::where('shift_id', $shift->id)
        ->where('status', 'completed')
        ->selectRaw("
            COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_sales,
            COUNT(*) as tx_count
        ")
        ->first();

    $cashSales = (float) $summary->cash_sales;
    $txCount   = (int) $summary->tx_count;

    // Top 5 items sold in this shift
    $topItems = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
        ->join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
        ->join('products', 'products.id', '=', 'product_variants.product_id')
        ->where('sales.shift_id', $shift->id)
        ->where('sales.status', 'completed')
        ->where('sale_items.is_returned', false)
        ->select([
            'products.name',
            'product_variants.sku',
            DB::raw('SUM(sale_items.quantity) as qty'),
            DB::raw('SUM(sale_items.line_total) as revenue'),
        ])
        ->groupBy('product_variants.id', 'products.name', 'product_variants.sku')
        ->orderByDesc('revenue')
        ->limit(5)
        ->get()
        ->map(fn($i) => [
            'name'    => $i->name,
            'sku'     => $i->sku,
            'qty'     => (int)$i->qty,
            'revenue' => (float)$i->revenue,
        ]);

    $expectedCash = $shift->starting_cash + $cashSales;

    return response()->json([
        'shift' => [
            'id'               => $shift->id,
            'starting_cash'    => (float)$shift->starting_cash,
            'expected_cash'    => (float)($shift->expected_cash ?? $expectedCash),
            'actual_cash'      => $shift->actual_cash !== null ? (float)$shift->actual_cash : null,
            'discrepancy'      => $shift->discrepancy !== null ? (float)$shift->discrepancy : null,
            'discrepancy_note' => $shift->discrepancy_note,
            'cash_sales'       => $cashSales,
            'tx_count'         => $txCount,
            'duration'         => $shift->closed_at
                ? $this->formatDuration($shift->opened_at, $shift->closed_at)
                : null,
        ],
        'top_items' => $topItems,
    ]);
}

    // ══════════════════════════════════════════
    //  REPORT — full shift Z-report page
    //  GET /pos/shifts/{shift}/report
    // ══════════════════════════════════════════
    public function report(Shift $shift)
{
    $shift->load('user');

    // One query for all the numeric summaries
    $summary = Sale::where('shift_id', $shift->id)
        ->whereIn('status', ['completed', 'refunded'])
        ->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_sales,
            COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'loan' THEN total_amount ELSE 0 END), 0) as loan_sales,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN discount_amount ELSE 0 END), 0) as discounts,
            COALESCE(SUM(CASE WHEN status = 'refunded' THEN total_amount ELSE 0 END), 0) as returns,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as tx_count
        ")
        ->first();

    $cashSales  = (float) $summary->cash_sales;
    $loanSales  = (float) $summary->loan_sales;
    $totalSales = $cashSales + $loanSales;
    $discounts  = (float) $summary->discounts;
    $returns    = (float) $summary->returns;
    $txCount    = (int) $summary->tx_count;
    $avgTicket  = $txCount > 0 ? $totalSales / $txCount : 0;

    // Items sold quantity
    $itemsSold = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
        ->where('sales.shift_id', $shift->id)
        ->where('sales.status', 'completed')
        ->sum('sale_items.quantity');

    // Top items – unchanged
    $topItems = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
        ->join('product_variants', 'product_variants.id', '=', 'sale_items.variant_id')
        ->join('products', 'products.id', '=', 'product_variants.product_id')
        ->where('sales.shift_id', $shift->id)
        ->where('sales.status', 'completed')
        ->select(['products.name', DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.line_total) as revenue')])
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('revenue')
        ->limit(10)
        ->get();

    $duration = $shift->closed_at
        ? $this->formatDuration($shift->opened_at, $shift->closed_at)
        : 'Still Active';

    return view('shifts.shiftReport', compact(
        'shift', 'cashSales', 'loanSales', 'totalSales', 'discounts',
        'returns', 'txCount', 'avgTicket', 'itemsSold', 'topItems', 'duration'
    ));
}

    // ══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════
    private function formatDuration(string $from, string $to): string
    {
        $mins = Carbon::parse($from)->diffInMinutes(Carbon::parse($to));
        $h    = floor($mins / 60);
        $m    = $mins % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }

    private function getAvgDuration(): string
    {
        $avg = Shift::where('is_closed', true)
            ->where('opened_at', '>=', now()->subDays(7))
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, closed_at)) as avg_mins')
            ->value('avg_mins');

        if (!$avg) return '—';
        $h = floor($avg / 60);
        $m = round($avg % 60);
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }

    private function getHijriDate(): string
    {
        try {
            $pashtoMonths = [
                1=>'وری',2=>'غویی',3=>'غبرګولی',4=>'چنګاښ',
                5=>'زمری',6=>'وږی',7=>'تله',8=>'لړم',
                9=>'لیندۍ',10=>'مرغومی',11=>'سلواغه',12=>'کب',
            ];
            $jalali = \Morilog\Jalali\Jalalian::fromDateTime(now());
            return $jalali->getYear().' '.$jalali->getDay().' '.($pashtoMonths[$jalali->getMonth()] ?? '');
        } catch (\Exception $e) {
            return '';
        }
    }
}
