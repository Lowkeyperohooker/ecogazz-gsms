<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Bryan Bacoy',
            'role' => 'Manager',
            'pin' => Hash::make('1234'), // Use Hash for security
        ]);

        // Create Staff
        User::create([
            'name' => 'Dodong',
            'role' => 'Pump Attendant',
            'pin' => Hash::make('0000'),
        ]);

        // Seed Inventory
        Product::create(['brand' => 'CALTEX', 'name' => 'TEXAMATIC 1L', 'cost_price' => 240, 'selling_price' => 290, 'stock_quantity' => 20]);
        Product::create(['brand' => 'SHELL', 'name' => '2T 200ML', 'cost_price' => 40, 'selling_price' => 50, 'stock_quantity' => 50]);
        Product::create(['brand' => 'PRYCEGAS', 'name' => '11KG REFILL', 'cost_price' => 950, 'selling_price' => 1100, 'stock_quantity' => 12]);
    }
}