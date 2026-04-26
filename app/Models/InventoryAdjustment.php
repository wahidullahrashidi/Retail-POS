<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = ['variant_id', 'adjustment_type', 'quantity', 'reason', 'reference_type', 'reference_id', 'adjusted_by', 'previous_stock', 'new_stock'];
    protected $casts = ['quantity' => 'integer', 'previous_stock' => 'integer', 'new_stock' => 'integer'];
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function adjuster()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
