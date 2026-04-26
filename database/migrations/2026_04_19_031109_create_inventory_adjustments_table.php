<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->enum('adjustment_type', ['increase', 'decrease', 'correction', 'damage', 'expiry', 'return_to_supplier']);
            $table->integer('quantity');
            $table->text('reason');
            $table->string('reference_type')->nullable();
            $table->foreignId('reference_id')->nullable();
            $table->foreignId('adjusted_by')->constrained('users');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
