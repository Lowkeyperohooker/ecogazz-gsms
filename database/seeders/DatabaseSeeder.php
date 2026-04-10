<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Pump;
use App\Models\FuelConfig;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CREATE USERS (From Jan 2026 Daily Sales)
        // ==========================================
        $users = [
            ['name' => 'Bryan Bacoy', 'role' => 'admin', 'pin' => 'amin'],
            ['name' => 'Dodong', 'role' => 'gasman', 'pin' => '1111'],
            ['name' => 'Kenneth', 'role' => 'gasman', 'pin' => '2222'],
            ['name' => 'Francis', 'role' => 'gasman', 'pin' => '3333'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'], 
                'role' => $u['role'],
                'pin' => Hash::make($u['pin']), 
                'is_active' => true
            ]);
        }

        // ==========================================
        // 2. CREATE PRODUCTS (From 2026 Inventory/Sales)
        // ==========================================
        $products = [
            // CALTEX
            ['brand' => 'CALTEX', 'name' => 'TEXAMATIC 1L', 'cost_price' => 240, 'selling_price' => 290, 'stock_quantity' => 20],
            ['brand' => 'CALTEX', 'name' => 'MARFAK GREASE 5KG', 'cost_price' => 160, 'selling_price' => 200, 'stock_quantity' => 5],
            ['brand' => 'CALTEX', 'name' => 'HAVOLINE 20W-40 1L', 'cost_price' => 200, 'selling_price' => 240, 'stock_quantity' => 15],
            ['brand' => 'CALTEX', 'name' => 'HAVOLINE EZY 20W-40 1L', 'cost_price' => 190, 'selling_price' => 239, 'stock_quantity' => 15],
            ['brand' => 'CALTEX', 'name' => 'HAVOLINE EZY 20W-40 800ML', 'cost_price' => 160, 'selling_price' => 198, 'stock_quantity' => 20],
            ['brand' => 'CALTEX', 'name' => 'SUPER DIESEL 1L', 'cost_price' => 160, 'selling_price' => 200, 'stock_quantity' => 25],
            ['brand' => 'CALTEX', 'name' => 'DELO GEAR EP4 90 1L', 'cost_price' => 240, 'selling_price' => 288, 'stock_quantity' => 10],
            ['brand' => 'CALTEX', 'name' => 'DELO GEAR EP 140 1L', 'cost_price' => 250, 'selling_price' => 300, 'stock_quantity' => 10],
            
            // WHIZ
            ['brand' => 'WHIZ', 'name' => 'BRAKE FLUID 900ML', 'cost_price' => 200, 'selling_price' => 240, 'stock_quantity' => 15],
            ['brand' => 'WHIZ', 'name' => 'BRAKE FLUID 270ML', 'cost_price' => 60, 'selling_price' => 79, 'stock_quantity' => 30],
            ['brand' => 'WHIZ', 'name' => 'BRAKE FLUID 150ML', 'cost_price' => 35, 'selling_price' => 50, 'stock_quantity' => 40],
            
            // CASTROL
            ['brand' => 'CASTROL', 'name' => 'GO ESSENTIAL 1L', 'cost_price' => 210, 'selling_price' => 256, 'stock_quantity' => 20],
            ['brand' => 'CASTROL', 'name' => 'GO ESSENTIAL 800ML', 'cost_price' => 180, 'selling_price' => 226, 'stock_quantity' => 20],
            ['brand' => 'CASTROL', 'name' => 'ACTIV 4T 1L', 'cost_price' => 230, 'selling_price' => 278, 'stock_quantity' => 15],
            
            // SHELL
            ['brand' => 'SHELL', 'name' => '2T 200ML', 'cost_price' => 40, 'selling_price' => 50, 'stock_quantity' => 50],
            
            // LPG - PRYCEGAS
            ['brand' => 'PRYCEGAS', 'name' => '11KG SET WITH CONTENT', 'cost_price' => 2200, 'selling_price' => 2600, 'stock_quantity' => 10],
            ['brand' => 'PRYCEGAS', 'name' => '11KG REFILL', 'cost_price' => 950, 'selling_price' => 1100, 'stock_quantity' => 25],
            
            // LPG - PETRON GASUL
            ['brand' => 'PETRON GASUL', 'name' => '11KG SET WITH CONTENT', 'cost_price' => 1700, 'selling_price' => 2000, 'stock_quantity' => 8],
            ['brand' => 'PETRON GASUL', 'name' => '11KG REFILL', 'cost_price' => 960, 'selling_price' => 1110, 'stock_quantity' => 20],
            ['brand' => 'PETRON GASUL', 'name' => '2.7KG SET WITH CONTENT', 'cost_price' => 1200, 'selling_price' => 1499, 'stock_quantity' => 5],
            
            // LPG - FIESTA GAS
            ['brand' => 'FIESTA GAS', 'name' => '11KG SET WITH CONTENT', 'cost_price' => 1700, 'selling_price' => 2000, 'stock_quantity' => 5],
            ['brand' => 'FIESTA GAS', 'name' => '11KG REFILL', 'cost_price' => 970, 'selling_price' => 1120, 'stock_quantity' => 15],
            
            // LPG - OTHERS
            ['brand' => 'POWER KALAN', 'name' => '2.7KG SET WITH CONTENT', 'cost_price' => 1000, 'selling_price' => 1300, 'stock_quantity' => 5],
            ['brand' => 'POWER KALAN', 'name' => '2.7KG REFILL', 'cost_price' => 300, 'selling_price' => 370, 'stock_quantity' => 10],
            ['brand' => 'ACCESSORIES', 'name' => 'BURNER', 'cost_price' => 500, 'selling_price' => 650, 'stock_quantity' => 10],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }

        // ==========================================
        // 3. CREATE PUMP UNITS
        // ==========================================
        $frontDigital = Pump::create(['name' => 'Front', 'type' => 'Digital']);
        $frontMechanical = Pump::create(['name' => 'Front', 'type' => 'Mechanical']);
        $backDigital = Pump::create(['name' => 'Back', 'type' => 'Digital']);
        $backMechanical = Pump::create(['name' => 'Back', 'type' => 'Mechanical']);

        // ==========================================
        // 4. CREATE FUEL CONFIGS (Matched to Kimaya pricing)
        // ==========================================
        // Cost prices are estimated; Selling prices are exact from your Jan 2026 data
        $fuels = [
            ['fuel_type' => 'Diesel', 'cost_price' => 50.00, 'selling_price' => 55.80],
            ['fuel_type' => 'Premium', 'cost_price' => 51.00, 'selling_price' => 56.50],
            ['fuel_type' => 'Regular', 'cost_price' => 50.00, 'selling_price' => 55.80]
        ];

        // Attach fuels to Front Digital
        foreach ($fuels as $idx => $f) {
            FuelConfig::create(array_merge($f, ['pump_id' => $frontDigital->id, 'current_meter' => 1000 + ($idx * 500)]));
        }
        
        // Attach fuels to Front Mechanical
        foreach ($fuels as $idx => $f) {
            FuelConfig::create(array_merge($f, ['pump_id' => $frontMechanical->id, 'current_meter' => 500 + ($idx * 100)]));
        }

        // Attach fuels to Back Digital
        foreach ($fuels as $idx => $f) {
            FuelConfig::create(array_merge($f, ['pump_id' => $backDigital->id, 'current_meter' => 2000 + ($idx * 200)]));
        }

        // Attach fuels to Back Mechanical
        foreach ($fuels as $idx => $f) {
            FuelConfig::create(array_merge($f, ['pump_id' => $backMechanical->id, 'current_meter' => 800 + ($idx * 50)]));
        }
    }
}