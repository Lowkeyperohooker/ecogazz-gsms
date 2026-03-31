<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the incoming Vue data
        $validated = $request->validate([
            'date' => 'required|date',
            'schedule' => 'required|string',
            'gasman' => 'required|string',
            'gross_sales' => 'required|numeric',
            'total_deductions' => 'required|numeric',
            'net_remittance' => 'required|numeric',
            'fuel_sales' => 'array',
            'item_sales' => 'array',
            'deductions' => 'array',
        ]);

        try {
            // 2. Use a Database Transaction to ensure everything saves, or nothing saves
            DB::beginTransaction();

            // Create the main Shift record
            $shift = Shift::create([
                'date' => $validated['date'],
                'schedule' => $validated['schedule'],
                'gasman' => $validated['gasman'],
                'gross_sales' => $validated['gross_sales'],
                'total_deductions' => $validated['total_deductions'],
                'net_remittance' => $validated['net_remittance'],
                // Save deductions as a JSON column in your database
                'deductions_json' => json_encode($validated['deductions']),
                'status' => 'Pending', // Defaults to pending until Admin approves
            ]);

            // Save Fuel Sales (Assuming you have a relationship set up)
            foreach ($validated['fuel_sales'] as $fuel) {
                $shift->fuelSales()->create([
                    'pump' => $fuel['pump'],
                    'fuel_type' => $fuel['fuel_type'],
                    'liters' => $fuel['liters'],
                    'amount' => $fuel['amount'],
                ]);
            }

            // Save Item Sales
            foreach ($validated['item_sales'] as $item) {
                $shift->itemSales()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Shift successfully recorded!',
                'shift' => $shift->load('fuelSales', 'itemSales')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save shift.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}