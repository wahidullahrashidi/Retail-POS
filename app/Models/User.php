<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'username',
        'password',
        'photo',
        'pin_code',
        'permissions',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // App\Models\User.php
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin' || $this->hasPermission('*');
    }

    public function hasPermission(string $permission): bool
    {
        $userPermissions = $this->permissions ?? [];

        if ($this->matchesPermission($userPermissions, $permission)) {
            return true;
        }

        return (bool) $this->role?->hasPermission($permission);
    }

    public function hasAnyPermission(array|string $permissions): bool
    {
        foreach ((array) $permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    private function matchesPermission(array $allowedPermissions, string $permission): bool
    {
        if (in_array('*', $allowedPermissions, true)) {
            return true;
        }

        foreach ($allowedPermissions as $allowed) {
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
