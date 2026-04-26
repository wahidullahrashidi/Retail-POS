<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $fillable = ['loan_id', 'amount', 'received_by', 'notes', 'receipt_number'];
    protected $casts = ['amount' => 'decimal:2'];
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
