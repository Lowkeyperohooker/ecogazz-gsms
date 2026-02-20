<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
    use HasFactory;
    protected $fillable = ['fuel_type', 'price', 'effective_date'];
    
    // Helper to get current price
    public static function getCurrentPrice($fuelType) {
        return self::where('fuel_type', $fuelType)
                   ->orderBy('effective_date', 'desc')
                   ->first()
                   ->price ?? 0;
    }
}
