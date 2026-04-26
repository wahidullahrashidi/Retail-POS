<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = ['return_id', 'sale_item_id', 'quantity', 'refund_price', 'line_total'];
    protected $casts = ['refund_price' => 'decimal:2', 'line_total' => 'decimal:2'];
    public function returnRecord()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }
    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }
}
