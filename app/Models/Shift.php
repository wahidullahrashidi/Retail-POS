<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['user_id', 'opened_at', 'closed_at', 'starting_cash', 'expected_cash', 'actual_cash', 'discrepancy', 'discrepancy_note', 'is_closed', 'closed_by'];
    protected $casts = ['opened_at' => 'datetime', 'closed_at' => 'datetime', 'starting_cash' => 'decimal:2', 'expected_cash' => 'decimal:2', 'actual_cash' => 'decimal:2', 'discrepancy' => 'decimal:2', 'is_closed' => 'boolean'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
