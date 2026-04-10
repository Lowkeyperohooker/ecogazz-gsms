<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pump;
use App\Models\FuelConfig;

class PumpController extends Controller
{
    public function index()
    {
        $pumps = Pump::with('fuelConfigs')->get();
        return response()->json($pumps);
    }

    // === NEW: Handles Pricing & Meter Adjustments ===
    public function updateConfigs(Request $request)
    {
        $configs = $request->input('configs');

        foreach ($configs as $configData) {
            $fuelConfig = FuelConfig::find($configData['id']);
            
            if ($fuelConfig) {
                $fuelConfig->update([
                    'cost_price' => $configData['cost_price'],
                    'selling_price' => $configData['selling_price'],
                    'current_meter' => $configData['current_meter']
                ]);
            }
        }

        return response()->json(['message' => 'Configurations successfully updated!']);
    }
}