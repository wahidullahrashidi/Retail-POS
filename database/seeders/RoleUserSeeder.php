<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'permissions' => ['*'],
            ]
        );

        $managerRole = Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'permissions' => ['pos.*', 'inventory.*', 'reports.*', 'loan.*'],
            ]
        );

        $cashierRole = Role::updateOrCreate(
            ['name' => 'cashier'],
            [
                'display_name' => 'Cashier',
                'permissions' => ['pos.sale', 'pos.return', 'pos.hold', 'loan.payment'],
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'role_id' => $adminRole->id,
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'pin_code' => '9999',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'ahmad'],
            [
                'role_id' => $cashierRole->id,
                'name' => 'Ahmad Shah',
                'email' => 'ahmad@example.com',
                'password' => 'cashier123',
                'pin_code' => '1234',
                'is_active' => true,
            ]
        );
    }
}
