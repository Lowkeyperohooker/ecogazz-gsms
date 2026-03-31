<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'type'];

    /**
     * A Pump (e.g., "Front") has many Fuel Configurations (Nozzles).
     * e.g., Front Diesel, Front Premium, Front Regular.
     */
    public function fuelConfigs()
    {
        return $this->hasMany(FuelConfig::class);
    }
}