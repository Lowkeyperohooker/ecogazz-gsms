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
        User::create(['name' => 'Bryan Bacoy', 'role' => 'Manager', 'pin' => Hash::make('admin')]);
        User::create(['name' => 'Dodong', 'role' => 'Pump Attendant', 'pin' => Hash::make('staf')]);

        // 2. Create Products (Inventory)
        Product::create(['brand' => 'CALTEX', 'name' => 'TEXAMATIC 1L', 'cost_price' => 240, 'selling_price' => 290, 'stock_quantity' => 20]);
        Product::create(['brand' => 'SHELL', 'name' => '2T 200ML', 'cost_price' => 40, 'selling_price' => 50, 'stock_quantity' => 50]);

        // 3. Create Pumps
        $frontPump = Pump::create(['name' => 'Front', 'type' => 'Digital']);
        $backPump = Pump::create(['name' => 'Back', 'type' => 'Mechanical']);

        // 4. Create Fuel Configs (Nozzles) linked to Pumps
        FuelConfig::create(['pump_id' => $frontPump->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1500]);
        FuelConfig::create(['pump_id' => $frontPump->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 2000]);
        FuelConfig::create(['pump_id' => $frontPump->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 1000]);

        FuelConfig::create(['pump_id' => $backPump->id, 'fuel_type' => 'Diesel', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 800]);
        FuelConfig::create(['pump_id' => $backPump->id, 'fuel_type' => 'Premium', 'cost_price' => 52.50, 'selling_price' => 56.50, 'current_meter' => 500]);
        FuelConfig::create(['pump_id' => $backPump->id, 'fuel_type' => 'Regular', 'cost_price' => 52.00, 'selling_price' => 55.80, 'current_meter' => 300]);
    }
}