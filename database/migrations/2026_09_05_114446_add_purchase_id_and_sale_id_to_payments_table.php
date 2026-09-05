<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('purchase_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('purchases')
                ->restrictOnDelete();

            $table->foreignId('sale_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('sales')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['sale_id']);

            $table->dropColumn([
                'purchase_id',
                'sale_id',
            ]);
        });
    }
};