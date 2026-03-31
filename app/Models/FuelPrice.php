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

    // A FuelConfig belongs to a specific Pump (e.g., Front or Back)
    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    // A FuelConfig has many Pump Readings
    public function pumpReadings()
    {
        return $this->hasMany(PumpReading::class);
    }
}