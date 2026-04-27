<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Services\ReceiptService;
use App\Services\SaleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class POSController extends Controller
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

        $profitPercentage = 0;
        if ($yesterdaySales !== 0) {
            $profitPercentage = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        }

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

        try {
            $netProfitPercentage = (($netProfitToday - $netProfitYesterday) / $netProfitYesterday) * 100;
        } catch (\DivisionByZeroError $e) {
            $netProfitPercentage = 100;
        }

        // customers:
        $customersToday = Customer::whereDate('created_at', today())->count();
        // dd($customersToday);
        $customersYesterday = Customer::whereDate('created_at', Carbon::yesterday())->count();


        try {
            $loanPercentage = (($todayLoan - $yesterdayLoan) / $yesterdayLoan) * 100;
            $customersPercentage = (($customersToday - $customersYesterday) / $customersYesterday) * 100;
        } catch (\DivisionByZeroError $e) {
            $loanPercentage = 100;
            $customersPercentage = 100;
        }

        // recent transactions:

        $transactions = Loan::recentTransactions()->get();

        // low stock alert:
        $lowStock = ProductVariant::lowStack()->get();

        return [
            'todaySales' => $todaySales,
            'yesterdaySales' => $yesterdaySales,
            'profitPercentage' => $profitPercentage,
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

    public function searchByBarcode($barcode)
    {
        $variant = ProductVariant::with(['product', 'attr1', 'attr2'])
            ->where(function ($query) use ($barcode) {
                $query->where('barcode', $barcode)
                    ->orWhereJsonContains('additional_barcodes', $barcode);
            })
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->first();

        if (! $variant) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $variant->id,
            'name' => $variant->product->name,
            'variant_name' => $this->getVariantName($variant),
            'price' => $variant->price ?? $variant->product->base_price,
            'stock' => $variant->stock_quantity,
            'barcode' => $variant->barcode,
        ]);
    }

    public function storeSale(Request $request, SaleService $saleService)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:product_variants,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric',
            'payment_method' => 'required|in:cash,loan',
            'amount_paid' => 'nullable|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric',
        ]);

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->first();

        if (! $activeShift) {
            return response()->json(['error' => 'No active shift'], 400);
        }

        $sale = $saleService->createSale(
            $request->all(),
            $request->cart,
            auth()->id(),
            $activeShift->id
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'local_id' => $sale->local_id,
            'total' => $sale->total_amount,
        ]);
    }


    public function showAllCustomers()
    {
        $customers = Customer::allCustomers()->get();
        return view('pos.dashboard', ['customers', $customers]);
    }
}
