<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// These are in the Api folder!
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\PumpReadingController;
use App\Http\Controllers\Api\PumpController; 

// These were generated in the main Controllers folder
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // === THIS IS THE MISSING ROUTE FOR THE GASMAN NAME! ===
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // Staff Management
    Route::apiResource('users', UserController::class);

    // Shifts (POS Submission & Approvals)
    Route::apiResource('shifts', ShiftController::class);
    
    // Inventory
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/bulk-update', [ProductController::class, 'bulkUpdate']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
    // Pumps
    Route::get('/pumps', [PumpController::class, 'index']); 
    Route::post('/pumps/update-configs', [PumpController::class, 'updateConfigs']);
    Route::post('/pumps/save-readings', [PumpController::class, 'saveReadings']);
    Route::get('/pumps/latest-readings', [PumpReadingController::class, 'index']);
}); 