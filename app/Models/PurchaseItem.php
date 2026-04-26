<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = ['purchase_id', 'variant_id', 'quantity_ordered', 'quantity_received', 'unit_cost', 'line_total', 'expiry_date', 'batch_number'];
    protected $casts = ['unit_cost' => 'decimal:2', 'line_total' => 'decimal:2', 'expiry_date' => 'date'];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
