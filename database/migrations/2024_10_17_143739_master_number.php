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
        Schema::table('customer_data', function (Blueprint $table) {
            //
            $table->string('master_number', 15); // Full number with prefix (max length 15)
            $table->string('user_id')->nullable();
            // $table->string('master_number')->nullable();
            // $table->string('rfs_status')->nullable();
            // $table->string('')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
