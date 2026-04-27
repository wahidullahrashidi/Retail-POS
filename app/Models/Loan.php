<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Loan extends Model
{
    protected $fillable = ['sale_id', 'customer_id', 'original_amount', 'amount_paid', 'remaining_balance', 'due_date', 'status', 'payment_count', 'last_payment_at'];
    protected $casts = ['original_amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'remaining_balance' => 'decimal:2', 'due_date' => 'date', 'last_payment_at' => 'datetime'];
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public static function recentTransactions($limit = 5)
    {
        $loanTransactions = DB::table('loans')
            ->join('customers', 'loans.customer_id', '=', 'customers.id')
            ->select([
                DB::raw("'loan' as type"),
                'customers.name as customer_name',
                'customers.address',
                'loans.original_amount as amount',
                DB::raw('NULL as receipt_number'),
                DB::raw('NULL as received_by'),
                'loans.created_at',
            ]);

        $paymentTransactions = DB::table('loan_payments')
            ->join('loans', 'loan_payments.loan_id', '=', 'loans.id')
            ->join('customers', 'loans.customer_id', '=', 'customers.id')
            ->join('users', 'loan_payments.received_by', '=', 'users.id')
            ->select([
                DB::raw("'payment' as type"),
                'customers.name as customer_name',
                'customers.address',
                'loan_payments.amount',
                'loan_payments.receipt_number',
                'users.name as received_by',
                'loan_payments.created_at',
            ]);

        return DB::query()
            ->fromSub(
                $loanTransactions->unionAll($paymentTransactions),
                'transactions'
            )
            ->orderByDesc('created_at')
            ->limit($limit);
    }
}
