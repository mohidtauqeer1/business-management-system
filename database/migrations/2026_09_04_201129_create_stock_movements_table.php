<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('type');
            // purchase, sale, adjustment, purchase_return, sale_return

            $table->decimal('quantity', 12, 2);

            $table->decimal('stock_before', 12, 2);

            $table->decimal('stock_after', 12, 2);

            $table->string('reference_type')->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'reference_type',
                'reference_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};