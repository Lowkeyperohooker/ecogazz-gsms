<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // The Gasman on duty
            $table->foreignId('manager_id')->nullable()->constrained('users'); // Manager who approves
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->decimal('total_expenses', 10, 2)->default(0);
            $table->decimal('cash_remitted', 10, 2)->default(0);
            $table->decimal('over_short_variance', 10, 2)->default(0); // Calculated at closing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};