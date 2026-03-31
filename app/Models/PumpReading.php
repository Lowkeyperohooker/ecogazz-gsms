<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumpReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'fuel_config_id',
        'start_meter',
        'close_meter',
        'calibration',
        'liters_sold',
        'total_amount',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function fuelConfig()
    {
        return $this->belongsTo(FuelConfig::class);
    }
}