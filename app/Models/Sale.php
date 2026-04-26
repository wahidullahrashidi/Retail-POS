<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['local_id', 'shift_id', 'user_id', 'customer_id', 'sale_type', 'status', 'subtotal', 'discount_amount', 'tax_amount', 'total_amount', 'payment_method', 'amount_paid', 'change_amount', 'loan_id', 'hold_code', 'hold_expires_at', 'receipt_printed', 'notes', 'sync_status', 'synced_at'];
    protected $casts = ['subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'change_amount' => 'decimal:2', 'hold_expires_at' => 'datetime', 'receipt_printed' => 'boolean', 'synced_at' => 'datetime'];
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    
}
