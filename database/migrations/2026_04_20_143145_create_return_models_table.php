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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_sale_id')->constrained('sales');
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->decimal('total_amount', 12, 2);
            $table->enum('refund_method', ['cash', 'store_credit']);
            $table->text('reason');
            $table->enum('condition', ['good', 'damaged', 'defective']);
            $table->integer('restock_quantity')->default(0);
            $table->foreignId('processed_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_models');
    }
};
