<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('name_ps')->nullable();
            $table->string('name_dr')->nullable();
            $table->text('description')->nullable();
            $table->boolean('has_variants')->default(false);
            $table->decimal('base_price', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->string('unit')->default('piece');
            $table->boolean('is_active')->default(true);
            $table->boolean('expiry_tracking')->default(false);
            $table->integer('low_stock_threshold')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};