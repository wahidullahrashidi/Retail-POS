<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'permissions'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        if (in_array('*', $permissions, true)) {
            return true;
        }

        foreach ($permissions as $allowed) {
            if ($allowed === $permission) {
                return true;
            }

            if (str_ends_with($allowed, '.*') && str_starts_with($permission, rtrim($allowed, '*'))) {
                return true;
            }
        }

        return false;
    }
}
