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
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('pump_id')->constrained('pumps');
            $table->enum('fuel_type', ['Diesel', 'Premium', 'Regular']);
            
            // Readings are precise to 2 decimal places
            $table->decimal('starting_reading', 12, 2);
            $table->decimal('closing_reading', 12, 2)->nullable();
            $table->decimal('calibration', 10, 2)->default(0); 
            
            // Calculated columns
            $table->decimal('net_liters', 10, 2)->nullable(); 
            $table->decimal('price_per_liter', 8, 2); 
            $table->decimal('total_amount', 12, 2)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pump_readings');
    }
};