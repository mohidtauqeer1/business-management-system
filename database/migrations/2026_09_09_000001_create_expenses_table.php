<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('reference_number')->unique()->nullable();
            $table->string('title');
            $table->string('category'); // rent, salaries, utilities, transport, misc, etc.
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'bank', 'card'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
