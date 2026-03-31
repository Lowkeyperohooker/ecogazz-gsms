<?php

use Illuminate\Support\Facades\Route;

// These three are in the Api folder!
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\PumpReadingController;

// These were generated in the main Controllers folder
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// ... your routes below stay exactly the same ...

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
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
    
    // Pumps
    Route::get('/pumps/latest-readings', [PumpReadingController::class, 'index']);
    // You can add a route here later to update pump configs (AdminPumps.vue)
});