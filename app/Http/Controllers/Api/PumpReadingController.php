<?php
// app/Http/Controllers/Api/PumpReadingController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PumpReading;
use App\Models\FuelConfig; // Make sure you have this model from the ERD!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PumpReadingController extends Controller
{
    /**
     * Get the latest closing readings for all pump nozzles.
     * This formats the data perfectly for the StaffPumps.vue component.
     */
    public function index()
    {
        // Fetch all fuel configurations along with their parent pump
        $fuelConfigs = FuelConfig::with('pump')->get();
        $latestReadings = [];

        foreach ($fuelConfigs as $config) {
            // Find the last recorded reading for this specific nozzle (e.g., Front Diesel)
            $lastReading = PumpReading::where('fuel_config_id', $config->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Start meter is either the last recorded close meter, or the base meter in the config table
            $startMeter = $lastReading ? $lastReading->close_meter : $config->current_meter;

            $latestReadings[] = [
                'id'    => $config->id,
                'pump'  => $config->pump->label, // e.g., 'Front'
                'type'  => $config->pump->type,  // e.g., 'Digital'
                'name'  => $config->fuel_type,   // e.g., 'Diesel'
                'price' => $config->selling_price,
                'start' => (float) $startMeter,
                'sold'  => 0, // Default value for Vue UI inputs
                'calib' => 0  // Default value for Vue UI inputs
            ];
        }

        return response()->json($latestReadings);
    }

    /**
     * Store a new pump reading. 
     * Includes sanitation logic to prevent typos.
     */
    public function store(Request $request)
    {
        // 1. Validation & Sanitation
        $validated = $request->validate([
            'shift_id'       => 'required|exists:shifts,id',
            'fuel_config_id' => 'required|exists:fuel_configs,id',
            'start_meter'    => 'required|numeric|min:0',
            'close_meter'    => [
                'required', 
                'numeric', 
                // Custom rule: Closing must be >= Starting
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < $request->start_meter) {
                        $fail("Sanitation Error: Closing reading ($value) cannot be lower than starting reading ({$request->start_meter}). Please check for typos.");
                    }
                },
            ],
            'calibration'    => 'nullable|numeric|min:0',
        ]);

        // 2. Perform Calculations
        $calibration = $validated['calibration'] ?? 0;
        $litersSold = ($validated['close_meter'] - $validated['start_meter']) - $calibration;
        
        // Fetch current price for this specific nozzle
        $config = FuelConfig::findOrFail($validated['fuel_config_id']);
        $totalAmount = $litersSold * $config->selling_price;

        // 3. Save to Database
        $reading = PumpReading::create([
            'shift_id'       => $validated['shift_id'],
            'fuel_config_id' => $validated['fuel_config_id'],
            'start_meter'    => $validated['start_meter'],
            'close_meter'    => $validated['close_meter'],
            'calibration'    => $calibration,
            'liters_sold'    => $litersSold,
            'total_amount'   => $totalAmount,
        ]);

        // 4. Update the Master Meter
        // Automatically save the new closing meter to the FuelConfig so the next shift starts here
        $config->update([
            'current_meter' => $validated['close_meter']
        ]);

        return response()->json([
            'message' => 'Pump reading saved successfully.',
            'data'    => $reading
        ], 201);
    }
}