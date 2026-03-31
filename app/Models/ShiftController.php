<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate incoming Vue data
        $validated = $request->validate([
            'date' => 'required|date',
            'schedule' => 'required|string',
            'gasman' => 'required|string', // Or user_id if you pass the ID
            'gross_sales' => 'required|numeric',
            'total_deductions' => 'required|numeric',
            'net_remittance' => 'required|numeric',
            'fuel_sales' => 'array',
            'item_sales' => 'array',
            'deductions' => 'array',
        ]);

        try {
            DB::beginTransaction();

            // 2. Create the main Shift record (Assuming Auth user is the gasman for now)
            $shift = Shift::create([
                'user_id' => auth()->id() ?? 1, // Fallback to 1 for testing without auth
                'shift_date' => $validated['date'],
                'schedule' => $validated['schedule'],
                'gross_sales' => $validated['gross_sales'],
                'total_deductions' => $validated['total_deductions'],
                'net_remittance' => $validated['net_remittance'],
                'status' => 'Pending',
            ]);

            // 3. Save Fuel Sales (Pump Readings)
            foreach ($validated['fuel_sales'] ?? [] as $fuel) {
                $shift->pumpReadings()->create([
                    // You'll need to look up the fuel_config_id based on the pump name in production
                    'fuel_config_id' => 1, 
                    'liters_sold' => $fuel['liters'],
                    'total_amount' => $fuel['amount'],
                ]);
            }

            // 4. Save Item Sales & Deduct Inventory Stock
            foreach ($validated['item_sales'] ?? [] as $item) {
                // Find product to deduct stock
                $product = Product::where('name', $item['product_name'])->first();
                
                if($product) {
                    $shift->itemSales()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'total_amount' => $item['amount'],
                    ]);
                    
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            // 5. Save Deductions
            foreach ($validated['deductions'] ?? [] as $category => $amount) {
                if ($amount > 0) {
                    $shift->deductions()->create([
                        'category' => strtoupper($category),
                        'amount' => $amount,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Shift successfully recorded!', 'shift' => $shift], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to save shift.', 'error' => $e->getMessage()], 500);
        }
    }
}