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

    $table->foreignId('customer_id')
        ->nullable()
        ->constrained('customers')
        ->nullOnDelete();

    $table->foreignId('user_id')
        ->constrained('users')
        ->restrictOnDelete();

    $table->date('sale_date');

    $table->string('invoice_number')->unique();

    $table->decimal('total_amount', 12, 2)->default(0);

    $table->decimal('discount', 12, 2)->default(0);

    $table->decimal('tax', 12, 2)->default(0);

    $table->decimal('paid_amount', 12, 2)->default(0);

    $table->enum('payment_status', [
        'paid',
        'partial',
        'unpaid'
    ])->default('unpaid');

    $table->enum('payment_method', [
        'cash',
        'card',
        'bank_transfer'
    ])->default('cash');

    $table->timestamps();
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
