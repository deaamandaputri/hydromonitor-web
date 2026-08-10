<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Sensor endpoints
Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::get('/sensor/latest', [SensorDataController::class, 'latest']);
Route::post('/pump/control', [SensorDataController::class, 'controlPump']);
