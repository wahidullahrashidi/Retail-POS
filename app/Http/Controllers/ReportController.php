<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ProductVariant;
use App\Models\Loan;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LoanPayment;

class ReportController extends Controller
{
    public function dashboard()
    {
        $today = now()->startOfDay();

        $data = [
            'today_sales' => Sale::whereDate('created_at', $today)->sum('total_amount'),
            'today_orders' => Sale::whereDate('created_at', $today)->count(),
            'low_stock_count' => ProductVariant::where('stock_quantity', '<', 10)->count(),
            'overdue_loans' => Loan::where('status', 'overdue')->count(),

            'sales_chart' => $this->getLast7DaysSales(),
            'top_products' => $this->getTopProducts(),
        ];

        return view('reports.dashboard', compact('data'));
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7));
        $endDate = $request->get('end_date', now());

        $sales = Sale::with(['user', 'customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('total_amount'),
            'loan_sales' => $sales->where('payment_method', 'loan')->sum('total_amount'),
        ];

        return view('reports.sales', compact('sales', 'summary', 'startDate', 'endDate'));
    }

    public function inventoryReport()
    {
        $products = ProductVariant::with(['product', 'attr1', 'attr2'])
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->paginate(50);

        $lowStock = ProductVariant::where('stock_quantity', '<', 10)->count();
        $outOfStock = ProductVariant::where('stock_quantity', 0)->count();

        return view('reports.inventory', compact('products', 'lowStock', 'outOfStock'));
    }

    public function loanReport()
    {
        $loans = Loan::with(['customer', 'sale'])
            ->where('status', 'active')
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        $overdue = Loan::where('status', 'overdue')->get();
        $totalOutstanding = Loan::whereIn('status', ['active', 'overdue'])->sum('remaining_balance');

        return view('reports.loans', compact('loans', 'overdue', 'totalOutstanding'));
    }

    private function getLast7DaysSales()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $amount = Sale::whereDate('created_at', $date)->sum('total_amount');
            $data[] = [
                'date' => $date->format('m-d'),
                'amount' => $amount
            ];
        }
        return $data;
    }

    private function getTopProducts()
    {
        return \DB::table('sale_items')
            ->select('variant_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('variant_id')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $variant = ProductVariant::with('product')->find($item->variant_id);
                return [
                    'name' => $variant->product->name,
                    'qty' => $item->total_qty
                ];
            });
    }

    public function recordLoanPayment(Request $request, $loanId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $loan = Loan::with('customer')->findOrFail($loanId);

        if ($loan->status === 'paid') {
            return back()->with('error', 'Loan already paid');
        }

        $paymentAmount = $request->amount;

        // Don't allow overpayment
        if ($paymentAmount > $loan->remaining_balance) {
            $paymentAmount = $loan->remaining_balance;
        }

        // Create payment record
        LoanPayment::create([
            'loan_id' => $loan->id,
            'amount' => $paymentAmount,
            'received_by' => auth()->id(),
            'notes' => $request->notes,
            'receipt_number' => 'LP-' . now()->format('Ymd') . '-' . rand(1000, 9999),
        ]);

        // Update loan
        $newPaid = $loan->amount_paid + $paymentAmount;
        $newRemaining = $loan->original_amount - $newPaid;
        $newStatus = $newRemaining <= 0 ? 'paid' : 'active';

        $loan->update([
            'amount_paid' => $newPaid,
            'remaining_balance' => $newRemaining,
            'status' => $newStatus,
            'payment_count' => $loan->payment_count + 1,
            'last_payment_at' => now(),
        ]);

        return back()->with('success', "Payment recorded: {$paymentAmount} ؋");
    }
}
