<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'name_ps',
        'name_dr',
        'description',
        'has_variants',
        'base_price',
        'cost_price',
        'unit',
        'is_active',
        'expiry_tracking',
        'low_stock_threshold',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'has_variants' => 'boolean',
            'is_active' => 'boolean',
            'expiry_tracking' => 'boolean',
            'base_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%" . "{$term}" . "%")
              ->orWhere('name_ps', 'LIKE',  "%" . "{$term}" . "%")
              ->orwhere('name_dr', 'LIKE',  "%" . "{$term}" . "%");
        })
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0);
    }
}