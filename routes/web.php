<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuiteController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PerformanceController;

Route::get('/suite', [SuiteController::class, 'index'])
    ->name('suite.index');
Route::post('/suite/import', [SuiteController::class, 'import'])
    ->name('suite.import');

Route::get('/tracking', [TrackingController::class, 'index'])
    ->name('tracking.index');

Route::post('/tracking/import', [TrackingController::class, 'import'])
    ->name('tracking.import');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
Route::get('/dashboard/detail', [DashboardController::class, 'detail'])
    ->name('dashboard.detail');

Route::get('/performance/kurir', [DashboardController::class, 'kurirPerformance'])
    ->name('performance.kurirPerformance');
Route::get('/', function () {
    return redirect()->route('dashboard');
});
