<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/api/chart-data', [\App\Http\Controllers\SensorDataController::class, 'chartData'])->middleware('auth')->name('api.chart');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/history', function () {
    $history = \App\Models\SensorData::latest()->paginate(50);
    return view('history', compact('history'));
})->middleware('auth')->name('history');
