<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pump;
use App\Models\FuelConfig;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    public function index()
    {
        $pumps = Pump::with('fuelConfigs')->get();
        return response()->json($pumps);
    }

    public function updateConfigs(Request $request)
    {
        $configs = $request->input('configs');

        foreach ($configs as $configData) {
            $config = FuelConfig::find($configData['id']);
            if ($config) {
                $config->update([
                    'cost_price' => $configData['cost_price'],
                    'selling_price' => $configData['selling_price'],
                    'current_meter' => $configData['current_meter'],
                ]);
            }
        }

        return response()->json(['message' => 'Pump Configurations Updated Successfully!']);
    }

    public function saveReadings(Request $request)
    {
        $readings = $request->input('readings');

        foreach ($readings as $reading) {
            $config = FuelConfig::find($reading['id']);
            
            // Overwrite the old start meter with the new close meter!
            if ($config && isset($reading['close_meter'])) {
                $config->update([
                    'current_meter' => $reading['close_meter']
                ]);
            }
        }

        return response()->json(['message' => 'Pump meters updated successfully!']);
    }
}