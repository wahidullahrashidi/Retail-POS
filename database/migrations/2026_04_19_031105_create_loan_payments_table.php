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
        Schema::create('loan_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 12, 2);
    $table->foreignId('received_by')->constrained('users');
    $table->text('notes')->nullable();
    $table->string('receipt_number')->unique();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
