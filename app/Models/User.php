<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. ADD THIS IMPORT

class User extends Authenticatable
{
    // 2. ADD 'HasApiTokens' HERE
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'role',
        'pin',
        'is_active',
    ];

    protected $hidden = [
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}