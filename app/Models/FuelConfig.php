<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'pump_id',
        'fuel_type',
        'cost_price',
        'selling_price',
        'current_meter',
    ];

    /**
     * THIS IS THE MISSING PIECE!
     * It tells Laravel that every FuelConfig belongs to a specific Pump.
     */
    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function pumpReadings()
    {
        return $this->hasMany(PumpReading::class);
    }
}