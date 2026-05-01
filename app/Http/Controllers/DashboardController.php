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
        $data['activeShift'] = $activeShift; // just add it to the array

        return view('pos.dashboard', $data);
    }


    private function getDashboardData(): array
    {
        $yesterdaySales = Sale::whereDate('created_at', Carbon::yesterday())
            ->sum('total_amount');

        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');

        // getting loans:
        // today
        $todayLoan = Loan::whereDate('created_at', today())->sum('remaining_balance');

        // yesterday:
        $yesterdayLoan = Loan::whereDate('created_at', Carbon::yesterday())
            ->sum('remaining_balance');

        // profit:
        $salesToday = SaleItem::salesToday();
        $costToday = SaleItem::costOfTodaysSales();

        $salesYesterday = SaleItem::salesYesterday();
        $costYesterday = SaleItem::costOfYesterday();

        $netProfitToday = $salesToday - $costToday;
        $netProfitYesterday = $salesYesterday - $costYesterday;

        // customers:
        $customersToday = Customer::whereDate('created_at', today())->count();
        $customersYesterday = Customer::whereDate('created_at', Carbon::yesterday())->count();

        // recent transactions:

        $transactions = Loan::recentTransactions()->get();

        // low stock alert:
        $lowStock = ProductVariant::lowStack()->get();

        try {
            $netProfitPercentage = (($netProfitToday - $netProfitYesterday) / $netProfitYesterday) * 100;
            $loanPercentage = ($todayLoan - $yesterdayLoan)/$yesterdayLoan * 100;
            $customersPercentage = ($customersYesterday - $customersToday)/$customersYesterday*100;

        } catch (\DivisionByZeroError $e) {
            $netProfitPercentage = 100;
            $loanPercentage = 100;
            $customersPercentage = 100;
        }

        return [
            'todaySales' => $todaySales,
            'yesterdaySales' => $yesterdaySales,
            'loanToday' => $todayLoan,
            'loanYesterday' => $yesterdayLoan,
            'loanPercentage' => $loanPercentage,
            'todaysCustomers' => $customersToday,
            'yesterdayCustomers' => $customersYesterday,
            'customersPercentage' => $customersPercentage,
            'netProfitToday' => $netProfitToday,
            'netProfitYesterday' => $netProfitYesterday,
            'netProfitPercentage' => $netProfitPercentage,
            'recentTransactions' => $transactions,
            'lowStock' => $lowStock
        ];
    }

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

        // Pass cart to your checkout view or process it
        // Option A — show a checkout confirmation page:
        return view('pos.checkout', [
            'cartItems' => $cartItems,
            'total'     => collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']),
        ]);

        // Option B — process immediately and create a Sale record:
        // $this->processSale($cartItems, $request);
        // return redirect()->route('pos.dashboard')->with('success', 'Sale completed!');
    }

    
}
