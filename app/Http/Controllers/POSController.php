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

class POSController extends Controller
{
    public function index()
    {
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->first();

        if (! $activeShift) {
            return redirect()->route('shift.open.form');
        }

        $yesterdaySales = Sale::whereDate('created_at', Carbon::yesterday())
            ->sum('total_amount');

        $todaySales = Sale::where('created_at', today())->sum('total_amount');

        $profitPercentage = 0;
        if ($yesterdaySales !== 0) {
            $profitPercentage = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        }

        // getting loans:
        // today
        $todayLoan = Loan::where('created_at', today())->sum('remaining_balance');

        // yesterday:
        $yesterdayLoan = Loan::where('created_at', Carbon::yesterday())
            ->sum('remaining_balance');



        // profit:
        $salesToday = SaleItem::salesToday();
        $costToday = SaleItem::costOfTodaysSales();
        
        $salesYesterday = SaleItem::salesYesterday();
        $costYesterday = SaleItem::costOfYesterday();

        $netProfitToday = $salesToday - $costToday;
        $netProfitYesterday = $salesYesterday - $costYesterday;

        try {
            $netProfitPercentage = (($netProfitToday - $netProfitYesterday)/$netProfitYesterday)*100;
        } catch(\DivisionByZeroError $e) {
            $netProfitPercentage = 0;
        }

        // customers:
        $customersToday = Customer::where('created_at', today())->count();
        $customersYesterday = Customer::where('created_at', Carbon::yesterday())->count();

        try {
            $loanPercentage = (($todayLoan - $yesterdayLoan) / $yesterdayLoan) * 100;
            $customersPercentage = (($customersToday - $customersYesterday) / $customersYesterday) * 100;
        } catch (\DivisionByZeroError $e) {
            $loanPercentage = 0;
            $customersPercentage = 0;
        }

        // recent transactions:
        $customers = Customer::customers()->get();
        return view('pos.dashboard', [
            'activeShift' => $activeShift,
            'todaySales' => $todaySales,
            'yesterdaySales' => $yesterdaySales,
            'profitPercentage' => $profitPercentage,
            'loan' => $todayLoan,
            'loanPercentage' => $loanPercentage,
            'todaysCustomers' => $customersToday,
            'cutomersPercentage' => $customersPercentage,
            'customersPercentage' => $customersPercentage,
            'netProfitToday' => $netProfitToday,
            'netProfit' => $netProfitToday,
            'netProfitPercentage' => $netProfitPercentage,
            'customers' => $customers
        ]);
    }

    public function searchProduct(Request $request)   // here we have (Request $request)
    {
        $variants = ProductVariant::with(['product', 'attr1', 'attr2'])
            ->search($request->query)
            ->isActive()
            ->inStock()
            ->limit(10)
            ->get();

        return response()->json($variants);
        
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
