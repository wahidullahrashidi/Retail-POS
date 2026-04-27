<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'variant_id', 'quantity', 'unit_price', 'cost_price', 'discount_amount', 'line_total', 'is_returned', 'returned_qty'];
    protected $casts = ['unit_price' => 'decimal:2', 'cost_price' => 'decimal:2', 'discount_amount' => 'decimal:2', 'line_total' => 'decimal:2', 'is_returned' => 'boolean'];
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function scopeSalesToday($query)
    {
        return $query->whereDate('created_at', today())->sum('line_total');
    }

    public function scopeCostOfTodaysSales($query)
    {
        return $query->whereDate('created_at', today())->sum('cost_price');
    }

    public function scopeSalesYesterday($query)
    {
        return $query->whereDate('created_at', Carbon::yesterday())->sum('line_total');
    }

    public function scopeCostOfYesterday($query)
    {
        return $query->whereDate('created_at', Carbon::yesterday())->sum('cost_price');
    }

}
