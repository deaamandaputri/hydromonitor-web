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
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->string('pompa1_status', 10)->nullable(); // Max 10 chars
            $table->string('pompa2_status', 10)->nullable(); // Max 10 chars
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->dropColumn('pompa1_status');
            $table->dropColumn('pompa2_status');
        });
    }
};
