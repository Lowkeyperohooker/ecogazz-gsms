<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = []; // Or your fillable array

    // 1. Links the shift to the Gasman
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. Links the shift to the Fuel sold
    public function pumpReadings()
    {
        return $this->hasMany(PumpReading::class);
    }

    // 3. Links the shift to the Items sold
    public function itemSales()
    {
        return $this->hasMany(ItemSale::class); // Make sure your model is named ItemSale!
    }

    // 4. Links the shift to Deductions
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }
}