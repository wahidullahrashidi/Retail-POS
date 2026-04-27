<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'additional_barcodes',
        'attribute_value_1',
        'attribute_value_2',
        'attribute_value_3',
        'attribute_value_4',
        'price',
        'cost_price',
        'stock_quantity',
        'expiry_date',
        'batch_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'additional_barcodes' => 'array',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attr1()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_1');
    }

    public function attr2()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_2');
    }

    public function attr3()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_3');
    }

    public function attr4()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_4');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($builder) use ($search) {
            $builder->where('barcode', $search)
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ps', 'like', "%{$search}%")
                        ->orWhere('name_dr', 'like', "%{$search}%");
                });
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStack($query)
    {
        return $query->where('stock_quantity', '<', 5)
        ->select('sku', 'stock_quantity');
    }
}
