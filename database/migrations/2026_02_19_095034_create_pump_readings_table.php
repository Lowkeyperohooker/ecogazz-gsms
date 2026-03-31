<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pump_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fuel_config_id')->constrained()->cascadeOnDelete(); // NEW
            $table->decimal('start_meter', 12, 2); // Changed from starting_reading
            $table->decimal('close_meter', 12, 2); // Changed from closing_reading
            $table->decimal('calibration', 10, 2)->default(0);
            $table->decimal('liters_sold', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pump_readings');
    }
};