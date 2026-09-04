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
        Schema::create('products', function (Blueprint $table) {
              $table->id();
              $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
              $table->string('name', 150);
              $table->string('sku')->unique();
              $table->decimal('purchase_price', 12, 2)->default(0);
              $table->decimal('selling_price', 12, 2)->default(0);
              $table->decimal('stock_quantity', 12, 2)->default(0);
              $table->string('unit')->default('pcs');
              $table->decimal('reorder_level', 12, 2)->default(0);
              $table->enum('status', ['active', 'discontinued'])->default('active');
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
