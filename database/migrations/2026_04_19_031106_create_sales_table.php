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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique();
            $table->foreignId('shift_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->enum('sale_type', ['regular', 'loan', 'return'])->default('regular');
            $table->enum('status', ['completed', 'held', 'cancelled', 'refunded'])->default('completed');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['cash', 'loan']);
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->foreignId('loan_id')->nullable()->constrained();
            $table->string('hold_code')->nullable();
            $table->timestamp('hold_expires_at')->nullable();
            $table->boolean('receipt_printed')->default(true);
            $table->text('notes')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
