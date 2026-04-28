<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\CssSelector\Node\FunctionNode;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'phone_secondary',
        'address',
        'city',
        'notes',
        'credit_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'credit_limit' => 'decimal:2',
        ];
    }

    public function loans() 
    {
        return $this->hasMany(Loan::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%" . "{$term}" . "%")
        ->orwhere('city', 'LIKE',  "%" . "{$term}" . "%")
        ->orwhere('phone', 'LIKE',  "%" . "{$term}" . "%")
        ->orwhere('address', 'LIKE',  "%" . "{$term}" . "%");
    }
    
    public function scopeCustomers($query)
    {
        return $query->select('name', 'address', 'credit_limit', 'updated_at')->limit(5);
    }

    public function scopeAllCustomers($query)
    {
        return $query->select('name', 'address', 'credit_limit', 'updated_at');
    }
}