<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
