<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;
use App\Models\Product;
use App\Models\Pump;
use App\Models\FuelConfig;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * THIS WAS MISSING! 
     * Fetches all shifts for the Admin Dashboard
     */
    public function index()
    {
        $shifts = Shift::with(['user', 'pumpReadings.fuelConfig.pump', 'itemSales.product', 'deductions'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($shifts);
    }

    /**
     * Update shift status (Pending -> Approved)
     */
    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Approved'
        ]);

        $shift->update(['status' => $validated['status']]);

        return response()->json(['message' => 'Shift updated successfully', 'shift' => $shift]);
    }

    /**
     * Store the POS submission from the Staff POS
     */
    public function store(Request $request)
    {
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
            DB::beginTransaction();

            $user = User::where('name', 'LIKE', '%' . $validated['gasman'] . '%')->first();
            $userId = $user ? $user->id : auth()->id();

            $shift = Shift::create([
                'user_id' => $userId,
                'shift_date' => $validated['date'],
                'schedule' => $validated['schedule'],
                'gross_sales' => $validated['gross_sales'],
                'total_deductions' => $validated['total_deductions'],
                'net_remittance' => $validated['net_remittance'],
                'status' => 'Pending',
            ]);

            if (!empty($validated['fuel_sales'])) {
                foreach ($validated['fuel_sales'] as $fuel) {
                    $pump = Pump::where('name', $fuel['pump'])->first();
                    if ($pump) {
                        $config = FuelConfig::where('pump_id', $pump->id)
                            ->where('fuel_type', $fuel['fuel_type'])
                            ->first();

                        if ($config) {
                            $shift->pumpReadings()->create([
                                'fuel_config_id' => $config->id,
                                'start_meter' => 0, 
                                'close_meter' => 0,
                                'liters_sold' => $fuel['liters'],
                                'total_amount' => $fuel['amount'],
                                'calibration' => 0
                            ]);
                        }
                    }
                }
            }

            if (!empty($validated['item_sales'])) {
                foreach ($validated['item_sales'] as $item) {
                    $parts = explode(' ', $item['product_name'], 2);
                    $searchName = count($parts) > 1 ? $parts[1] : $item['product_name'];
                    
                    $product = Product::where('name', 'LIKE', '%' . $searchName . '%')->first();

                    if ($product) {
                        $shift->itemSales()->create([
                            'product_id' => $product->id,
                            'quantity' => $item['quantity'],
                            'total_amount' => $item['amount'],
                        ]);
                        $product->decrement('stock_quantity', $item['quantity']);
                    }
                }
            }

            if (!empty($validated['deductions'])) {
                foreach ($validated['deductions'] as $category => $amount) {
                    if (is_numeric($amount) && $amount > 0) {
                        $shift->deductions()->create([
                            'category' => strtoupper($category),
                            'amount' => $amount,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Shift successfully recorded!',
                'shift' => $shift->load('pumpReadings', 'itemSales', 'deductions')
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