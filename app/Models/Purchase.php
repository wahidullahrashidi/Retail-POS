<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['local_id', 'supplier_id', 'reference_number', 'purchase_date', 'delivery_date', 'status', 'total_cost', 'amount_paid', 'payment_status', 'notes', 'invoice_image', 'received_by', 'created_by', 'sync_status'];
    protected $casts = ['purchase_date' => 'date', 'delivery_date' => 'date', 'total_cost' => 'decimal:2', 'amount_paid' => 'decimal:2'];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
