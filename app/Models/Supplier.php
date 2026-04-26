<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'phone', 'phone_secondary', 'email', 'address', 'city', 'payment_terms', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
