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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('starting_cash', 12, 2);
            $table->decimal('expected_cash', 12, 2)->nullable();
            $table->decimal('actual_cash', 12, 2)->nullable();
            $table->decimal('discrepancy', 12, 2)->nullable();
            $table->text('discrepancy_note')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
