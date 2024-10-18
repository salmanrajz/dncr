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
            // Adding indexes for the specified columns
            $table->index('number');
            $table->index('prefix');
            $table->index('isWhatsApp');
            $table->index('isDncr');
            $table->index('status');
            $table->index('master_number');
            $table->index('user_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            //
            $table->dropIndex(['number']);
            $table->dropIndex(['prefix']);
            $table->dropIndex(['isWhatsApp']);
            $table->dropIndex(['isDncr']);
            $table->dropIndex(['status']);
            $table->dropIndex(['master_number']);
            $table->dropIndex(['user_id']);
        });
    }
};
