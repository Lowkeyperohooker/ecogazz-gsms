<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\PumpReading;

class DashboardController extends Controller
{
    public function getStats()
    {
        // Calculate Today's stats
        $todayShifts = Shift::whereDate('shift_date', today())->get();
        
        $revenue = $todayShifts->sum('gross_sales');
        $profit = $revenue * 0.07; // Assuming an estimated 7% margin
        $pending = Shift::where('status', 'Pending')->count();
        
        // Calculate Total Volume Dispensed Today
        $volume = PumpReading::whereHas('shift', function($query) {
            $query->whereDate('shift_date', today());
        })->sum('liters_sold');

        // Mock Chart Data (You can replace this with a GroupBy Week query later)
        $chartData = [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'sales' => [125000, 145000, 138000, 158000],
            'profit' => [12500, 14500, 13800, 15800]
        ];

        return response()->json([
            'revenue' => $revenue,
            'profit' => $profit,
            'pending' => $pending,
            'volume' => $volume,
            'chartData' => $chartData
        ]);
    }
}