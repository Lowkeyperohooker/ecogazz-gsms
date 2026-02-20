<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PumpReadingController;

// Temporarily leaving these outside of auth:sanctum so we can test the UI!
Route::get('/pumps/latest-readings', [PumpReadingController::class, 'getLatestReadings']);
Route::post('/pump-readings', [PumpReadingController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');