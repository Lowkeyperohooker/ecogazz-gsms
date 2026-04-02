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
        // 1. Create Users
        User::create([
            'name' => 'Bryan Bacoy', 
            'role' => 'admin',
            'pin' => Hash::make('amin'), 
            'is_active' => true
        ]);

        User::create([
            'name' => 'Dodong', 
            'role' => 'gasman',
            'pin' => Hash::make('staf'), 
            'is_active' => true
        ]);

        // 2. Create Products (Inventory)
        Product::create(['brand' => 'CALTEX', 'name' => 'TEXAMATIC 1L', 'cost_price' => 240, 'selling_price' => 290, 'stock_quantity' => 20]);
        Product::create(['brand' => 'SHELL', 'name' => '2T 200ML', 'cost_price' => 40, 'selling_price' => 50, 'stock_quantity' => 50]);

        // 3. Create All 4 Pump Units
        $frontDigital = Pump::create(['name' => 'Front', 'type' => 'Digital']);
        $frontMechanical = Pump::create(['name' => 'Front', 'type' => 'Mechanical']);
        $backDigital = Pump::create(['name' => 'Back', 'type' => 'Digital']);
        $backMechanical = Pump::create(['name' => 'Back', 'type' => 'Mechanical']);

        // 4. Create Fuel Configs (Nozzles) for EACH Pump
        
        // --- Front Digital ---
        FuelConfig::create(['pump_id' => $frontDigital->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1500]);
        FuelConfig::create(['pump_id' => $frontDigital->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 2000]);
        FuelConfig::create(['pump_id' => $frontDigital->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1000]);

        // --- Front Mechanical ---
        FuelConfig::create(['pump_id' => $frontMechanical->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 500]);
        FuelConfig::create(['pump_id' => $frontMechanical->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 500]);
        FuelConfig::create(['pump_id' => $frontMechanical->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 500]);

        // --- Back Digital ---
        FuelConfig::create(['pump_id' => $backDigital->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1200]);
        FuelConfig::create(['pump_id' => $backDigital->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 1200]);
        FuelConfig::create(['pump_id' => $backDigital->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1200]);

        // --- Back Mechanical ---
        FuelConfig::create(['pump_id' => $backMechanical->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 800]);
        FuelConfig::create(['pump_id' => $backMechanical->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 500]);
        FuelConfig::create(['pump_id' => $backMechanical->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 300]);
    }
}