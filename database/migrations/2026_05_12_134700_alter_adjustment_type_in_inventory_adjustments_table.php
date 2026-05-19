<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            Schema::table('inventory_adjustments', function (Blueprint $table) {
                // سلیقې د اینم د تغیر لپاره دا طریقه کاروي
                $table->string('adjustment_type')->change();
            });
        } else {
            DB::statement("
            ALTER TABLE inventory_adjustments
            MODIFY adjustment_type ENUM(
                'increase', 'decrease', 'correction', 'damage', 'expiry', 'return_to_supplier', 'sale_return'
            ) NOT NULL
        ");
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE inventory_adjustments
            MODIFY adjustment_type ENUM(
                'increase',
                'decrease',
                'correction',
                'damage',
                'expiry',
                'return_to_supplier'
            ) NOT NULL
        ");
    }
};
