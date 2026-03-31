<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $guarded = []; // Allows mass assignment

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function pumpReadings() {
        return $this->hasMany(PumpReading::class);
    }

    public function itemSales() {
        return $this->hasMany(ItemSale::class);
    }

    public function deductions() {
        return $this->hasMany(Deduction::class);
    }
}