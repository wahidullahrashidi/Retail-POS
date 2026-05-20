<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use Carbon\Carbon;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->first();

        if (!$activeShift) {
            return redirect()->route('shift.open.form');
        }

        $data = $this->getDashboardData();
        $data['activeShift'] = $activeShift;

        return view('pos.dashboard', $data);
    }

    private function getDashboardData(): array
    {
        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();
        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        // -------------------------------------------------
        // 1. Sales – one query instead of two
        // -------------------------------------------------
        $saleStats = Sale::completed()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN total_amount ELSE 0 END), 0) as today_sales,
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN total_amount ELSE 0 END), 0) as yesterday_sales
            ", [
                $todayStart, $todayEnd,
                $yesterdayStart, $yesterdayEnd,
            ])
            ->first();

        $todaySales = (float) $saleStats->today_sales;
        $yesterdaySales = (float) $saleStats->yesterday_sales;

        // -------------------------------------------------
        // 2. Loans – one query instead of two
        // -------------------------------------------------
        $loanStats = Loan::whereBetween('created_at', [$yesterdayStart, $todayEnd])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN remaining_balance ELSE 0 END), 0) as today_loan,
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN remaining_balance ELSE 0 END), 0) as yesterday_loan
            ", [
                $todayStart, $todayEnd,
                $yesterdayStart, $yesterdayEnd,
            ])
            ->first();

        $todayLoan = (float) $loanStats->today_loan;
        $yesterdayLoan = (float) $loanStats->yesterday_loan;

        // -------------------------------------------------
        // 3. Cost of goods – one query instead of two
        // -------------------------------------------------
        $costStats = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$yesterdayStart, $todayEnd])
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN sales.created_at BETWEEN ? AND ? THEN sale_items.quantity * COALESCE(sale_items.cost_price, 0) ELSE 0 END), 0) as cost_today,
                COALESCE(SUM(CASE WHEN sales.created_at BETWEEN ? AND ? THEN sale_items.quantity * COALESCE(sale_items.cost_price, 0) ELSE 0 END), 0) as cost_yesterday
            ", [
                $todayStart, $todayEnd,
                $yesterdayStart, $yesterdayEnd,
            ])
            ->first();

        $costToday = (float) $costStats->cost_today;
        $costYesterday = (float) $costStats->cost_yesterday;

        // -------------------------------------------------
        // 4. Customers – one query instead of two
        // -------------------------------------------------
        $customerStats = Customer::whereBetween('created_at', [$yesterdayStart, $todayEnd])
            ->selectRaw("
                COALESCE(COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE NULL END), 0) as today_count,
                COALESCE(COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE NULL END), 0) as yesterday_count
            ", [
                $todayStart, $todayEnd,
                $yesterdayStart, $yesterdayEnd,
            ])
            ->first();

        $customersToday = (int) $customerStats->today_count;
        $customersYesterday = (int) $customerStats->yesterday_count;

        // -------------------------------------------------
        // Calculations that rely on the numbers above
        // -------------------------------------------------
        $netProfitToday = $todaySales - $costToday;
        $netProfitYesterday = $yesterdaySales - $costYesterday;

        try {
            $netProfitPercentage = $yesterdaySales != 0
                ? (($netProfitToday - $netProfitYesterday) / $netProfitYesterday) * 100
                : 100;
            $loanPercentage = $yesterdayLoan != 0
                ? (($todayLoan - $yesterdayLoan) / $yesterdayLoan) * 100
                : 100;
            $customersPercentage = $customersYesterday != 0
                ? (($customersToday - $customersYesterday) / $customersYesterday) * 100
                : 100;
        } catch (\DivisionByZeroError $e) {
            // Fallback – should never be reached now, but kept for safety
            $netProfitPercentage = 100;
            $loanPercentage = 100;
            $customersPercentage = 100;
        }

        // -------------------------------------------------
        // Non‑aggregated data (still separate, but fast)
        // -------------------------------------------------
        $transactions = Loan::recentTransactions()->get();
        $lowStockItems = ProductVariant::lowStack()->get();

        return [
            'todaySales'          => $todaySales,
            'yesterdaySales'      => $yesterdaySales,
            'loanToday'           => $todayLoan,
            'loanYesterday'       => $yesterdayLoan,
            'loanPercentage'      => $loanPercentage,
            'todaysCustomers'     => $customersToday,
            'yesterdayCustomers'  => $customersYesterday,
            'customersPercentage' => $customersPercentage,
            'netProfitToday'      => $netProfitToday,
            'netProfitYesterday'  => $netProfitYesterday,
            'netProfitPercentage' => $netProfitPercentage,
            'recentTransactions'  => $transactions,
            'lowStock'            => $lowStockItems,
        ];
    }

    // (The rest of your methods remain unchanged)
    public function searchProducts(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            return response()->json([]);
        }

        $variants = ProductVariant::query()
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.is_active', true)
            ->where('products.is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('products.name',    'like', "%{$q}%")
                    ->orWhere('products.name_ps',  'like', "%{$q}%")
                    ->orWhere('products.name_dr',  'like', "%{$q}%")
                    ->orWhere('product_variants.barcode', 'like', "%{$q}%")
                    ->orWhere('product_variants.sku',     'like', "%{$q}%");
            })
            ->select([
                'product_variants.id as variant_id',
                'products.name',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.stock_quantity',
                DB::raw('COALESCE(product_variants.price, 0) as price'),
            ])
            ->orderBy('products.name')
            ->limit(20)
            ->get();

        return response()->json($variants);
    }

    public function trendingProducts()
    {
        $variants = ProductVariant::query()
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('sale_items', 'sale_items.variant_id', '=', 'product_variants.id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.created_at', '>=', now()->subDays(7))
            ->where('sales.status', 'completed')
            ->where('sale_items.is_returned', false)
            ->where('product_variants.is_active', true)
            ->where('products.is_active', true)
            ->where('product_variants.stock_quantity', '>', 0)
            ->groupBy([
                'product_variants.id',
                'products.name',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.stock_quantity',
                'product_variants.price',
            ])
            ->orderByRaw('SUM(sale_items.quantity) DESC')
            ->select([
                'product_variants.id as variant_id',
                'products.name',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.stock_quantity',
                DB::raw('COALESCE(product_variants.price, 0) as price'),
                DB::raw('SUM(sale_items.quantity) as total_sold'),
            ])
            ->limit(8)
            ->get();

        return response()->json($variants);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart'   => 'required|string',
        ]);

        $cartItems = json_decode($request->input('cart'), true);

        if (empty($cartItems)) {
            return back()->with('error', 'Cart is empty.');
        }

        session()->flash('checkout_cart', $cartItems);

        return redirect()->route('pos.poscheck');
    }
}