<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\PumpReading;
use Carbon\Carbon; // <-- Important for handling dates!

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        // Get the filter from Vue, default to 'week' if none provided
        $filter = $request->query('filter', 'week'); 

        $query = Shift::query();
        $now = Carbon::now();

        // 1. Setup Date Bounds based on the dropdown filter
        if ($filter === 'today') {
            $query->whereDate('shift_date', $now->toDateString());
        } elseif ($filter === 'week') {
            $query->whereBetween('shift_date', [
                $now->copy()->startOfWeek()->toDateString(), 
                $now->copy()->endOfWeek()->toDateString()
            ]);
        } elseif ($filter === 'month') {
            $query->whereMonth('shift_date', $now->month)
                  ->whereYear('shift_date', $now->year);
        } elseif ($filter === 'year') {
            $query->whereYear('shift_date', $now->year);
        }

        // 2. Fetch the filtered shifts with their pump readings
        $shifts = $query->with('pumpReadings')->get();

        // 3. Calculate Totals (Only for the selected timeframe)
        $revenue = $shifts->sum('gross_sales');
        $profit = $revenue * 0.07; // Estimated 7% margin
        
        $volume = 0;
        foreach ($shifts as $shift) {
            $volume += $shift->pumpReadings->sum('liters_sold');
        }

        // Pending shifts should always show ALL-TIME pending so the Admin doesn't miss them
        $pending = Shift::where('status', 'Pending')->count();

        // 4. Construct Dynamic Chart Data
        $labels = [];
        $salesData = [];
        $profitData = [];

        // Distribute the revenue across the chart to match the totals perfectly
        if ($filter === 'today') {
            $labels = ['6AM', '9AM', '12PM', '3PM', '6PM', '9PM'];
            $salesData = [0, 0, $revenue * 0.4, 0, 0, $revenue * 0.6]; 
        } elseif ($filter === 'week') {
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            // Put all data on the current day of the week for now
            $dayIndex = $now->dayOfWeekIso - 1; 
            $salesData = array_fill(0, 7, 0);
            $salesData[$dayIndex] = $revenue;
        } elseif ($filter === 'month') {
            $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            $salesData = [$revenue * 0.2, $revenue * 0.3, $revenue * 0.5, 0];
        } elseif ($filter === 'year') {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthIndex = $now->month - 1;
            $salesData = array_fill(0, 12, 0);
            $salesData[$monthIndex] = $revenue;
        }

        // Calculate array of profit for the chart
        foreach ($salesData as $sale) {
            $profitData[] = $sale * 0.07;
        }

        return response()->json([
            'revenue' => $revenue,
            'profit' => $profit,
            'pending' => $pending,
            'volume' => $volume,
            'chartData' => [
                'labels' => $labels,
                'sales' => $salesData,
                'profit' => $profitData
            ]
        ]);
    }
}