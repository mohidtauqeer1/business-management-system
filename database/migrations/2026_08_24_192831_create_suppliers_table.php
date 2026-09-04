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
        

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();                                           // BIGINT UNSIGNED, auto-increment PK

            $table->string('name');                                 // Required: supplier company/person name

            $table->string('contact_person')->nullable();          // Optional: specific person to contact

            $table->string('phone', 20)->nullable();               // Optional: string (not int) to preserve formatting e.g. 0300-1234567
            $table->string('email')->nullable()->unique();         // Optional but unique: no two suppliers should share an email
            $table->text('address')->nullable();                   // Optional: text (not string) to support multi-line addresses

            $table->timestamps();                                  // created_at & updated_at (TIMESTAMP, nullable by default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
