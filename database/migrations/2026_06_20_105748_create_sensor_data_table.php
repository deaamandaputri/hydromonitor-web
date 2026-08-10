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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->float('proximity')->nullable();
            $table->float('water_level_cm')->nullable();
            $table->float('water_level_percent')->nullable();
            $table->integer('turbidity_adc')->nullable();
            $table->float('turbidity_voltage')->nullable();
            $table->string('water_status', 60)->nullable(); // Max 60 chars
            $table->float('flow_rate')->nullable();
            $table->float('total_litres')->nullable();
            $table->string('pump_status', 10)->nullable(); // Max 10 chars
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
