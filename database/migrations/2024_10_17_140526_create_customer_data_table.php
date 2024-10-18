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
        Schema::create('customer_data', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('number'); // Customer's Phone Number
            $table->string('prefix')->nullable(); // Prefix for the number (optional)
            $table->boolean('isWhatsApp')->default(false); // WhatsApp availability (true/false)
            $table->boolean('isDncr')->default(false); // DNCR (true/false)
            $table->enum('status', ['Interested', 'Follow Up', 'Not Answer','No Responsed Yet'])->default('No Responsed Yet'); // Status (with default value)
            $table->timestamps(); // Created at and Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_data');
    }
};
