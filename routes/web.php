<?php

use Illuminate\Support\Facades\Route;

// Catch-All Route: Sends all web requests to your Vue app
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');