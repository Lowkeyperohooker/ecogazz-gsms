<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'manager_id', 'start_time', 'end_time', 
        'status', 'total_expenses', 'cash_remitted', 'over_short_variance'
    ];

    public function readings() {
        return $this->hasMany(PumpReading::class);
    }
    
    public function attendant() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
