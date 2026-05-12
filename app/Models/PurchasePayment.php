<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'created_by',
    ];

    // ═══════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}