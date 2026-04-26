<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $cashier = User::where('username', 'ahmad')->firstOrFail();

        Shift::updateOrCreate(
            [
                'user_id' => $cashier->id,
                'is_closed' => false,
            ],
            [
                'opened_at' => now(),
                'starting_cash' => 5000,
            ]
        );
    }
}
