<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::updateOrCreate(
            ['phone' => '0701234567'],
            [
                'name' => 'Ahmad Shah',
                'phone_secondary' => '0799876543',
                'address' => 'Karte Parwan, Kabul',
                'city' => 'Kabul',
                'credit_limit' => 50000,
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['phone' => '0775558888'],
            [
                'name' => 'Mohammad Karim',
                'address' => 'Dasht-e Barchi, Kabul',
                'city' => 'Kabul',
                'credit_limit' => 30000,
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['phone' => '0781112222'],
            [
                'name' => 'Fatima Ahmadi',
                'address' => 'Shahr-e Naw, Kabul',
                'city' => 'Kabul',
                'credit_limit' => 20000,
                'is_active' => true,
            ]
        );
    }
}
