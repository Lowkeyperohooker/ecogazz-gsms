<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. THIS IMPORT IS CRITICAL

class User extends Authenticatable
{
    // 2. ADD 'HasApiTokens' INSIDE THIS USE STATEMENT
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     * 3. ADD YOUR NEW COLUMNS HERE
     */
    protected $fillable = [
        'name',
        'role',
        'pin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'pin', // Hide the PIN from JSON responses for security
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}