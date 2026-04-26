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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->string('reference_number')->nullable();
            $table->date('purchase_date');
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['ordered', 'partial', 'received', 'cancelled'])->default('ordered');
            $table->decimal('total_cost', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->string('invoice_image')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->timestamps();
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreign('purchase_id')->references('id')->on('purchases')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
