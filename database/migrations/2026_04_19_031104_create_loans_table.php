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
        if (Schema::hasTable('loans')) {
            return;
        }

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->decimal('original_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('remaining_balance', 12, 2);
            $table->date('due_date');
            $table->enum('status', ['active', 'paid', 'overdue'])->default('active');
            $table->integer('payment_count')->default(0);
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
