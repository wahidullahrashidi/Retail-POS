<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';
    protected $fillable = ['original_sale_id', 'customer_id', 'total_amount', 'refund_method', 'reason', 'condition', 'restock_quantity', 'processed_by'];
    protected $casts = ['total_amount' => 'decimal:2'];
    public function originalSale()
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
