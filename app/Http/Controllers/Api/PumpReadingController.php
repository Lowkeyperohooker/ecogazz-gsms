<?php
// app/Http/Controllers/Api/PumpReadingController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PumpReading;
use App\Models\Shift;
use App\Models\Pump;
use App\Models\FuelPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PumpReadingController extends Controller
{
    /**
     * Get the latest closing readings for all pumps to pre-fill the "Starting Reading" form.
     */
    public function getLatestReadings()
    {
        $pumps = Pump::all();
        $latestReadings = [];

        foreach ($pumps as $pump) {
            // Find the last recorded reading for this pump
            $lastReading = PumpReading::where('pump_id', $pump->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $latestReadings[] = [
                'pump_id' => $pump->id,
                'pump_name' => $pump->name,
                'pump_type' => $pump->type,
                // Default to 0 if no history exists (new system)
                'suggested_start_reading' => $lastReading ? $lastReading->closing_reading : 0 
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
            'shift_id' => 'required|exists:shifts,id',
            'pump_id' => 'required|exists:pumps,id',
            'fuel_type' => 'required|in:Diesel,Premium,Regular',
            'starting_reading' => 'required|numeric|min:0',
            'closing_reading' => [
                'required', 
                'numeric', 
                // Custom rule: Closing must be >= Starting
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < $request->starting_reading) {
                        $fail("Sanitation Error: Closing reading ($value) cannot be lower than starting reading ({$request->starting_reading}). Please check for typos.");
                    }
                },
            ],
            'calibration' => 'nullable|numeric|min:0',
        ]);

        // 2. Perform Calculations
        $calibration = $validated['calibration'] ?? 0;
        $netLiters = ($validated['closing_reading'] - $validated['starting_reading']) - $calibration;
        
        // Fetch current price
        $currentPrice = FuelPrice::getCurrentPrice($validated['fuel_type']);
        $totalAmount = $netLiters * $currentPrice;

        // 3. Save to Database
        $reading = PumpReading::create([
            'shift_id' => $validated['shift_id'],
            'pump_id' => $validated['pump_id'],
            'fuel_type' => $validated['fuel_type'],
            'starting_reading' => $validated['starting_reading'],
            'closing_reading' => $validated['closing_reading'],
            'calibration' => $calibration,
            'net_liters' => $netLiters,
            'price_per_liter' => $currentPrice,
            'total_amount' => $totalAmount,
        ]);

        return response()->json([
            'message' => 'Pump reading saved successfully.',
            'data' => $reading
        ], 201);
    }
}