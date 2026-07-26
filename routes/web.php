<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuiteController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\MonitoringStdController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\ContexHubController;


Auth::routes();

Route::middleware(['auth','active'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/dashboard/detail', [DashboardController::class,'detail'])
        ->name('dashboard.detail');

    Route::get('/suite', [SuiteController::class,'index'])
        ->name('suite.index');

    Route::post('/suite/import', [SuiteController::class,'import'])
        ->name('suite.import');

    Route::get('/tracking', [TrackingController::class,'index'])
        ->name('tracking.index');

    Route::post('/tracking/import', [TrackingController::class,'import'])
        ->name('tracking.import');

    Route::get('/performance/kurir',[DashboardController::class,'kurirPerformance'])
        ->name('performance.index');

    Route::get('/monitoring-std',[MonitoringStdController::class,'index'])
        ->name('monitoring.index');

    Route::post('/monitoring-std/import',[MonitoringStdController::class,'import'])
        ->name('monitoring.import');

    Route::delete('/monitoring-std/reset',[MonitoringStdController::class,'reset'])
        ->name('monitoring.reset');

    Route::get('/monitoring-std/driver/{driverId}',
        [MonitoringStdController::class,'driverDetail'])
        ->name('monitoring.driver');

    Route::get('/home',[App\Http\Controllers\HomeController::class,'index'])
        ->name('home');

});

Route::middleware(['auth','active','role:owner'])->group(function () {

  // Master Hub
    Route::resource('hub', HubController::class);

    // Master User
    Route::resource('master-user', MasterUserController::class);

    Route::patch(
        'master-user/{user}/toggle',
        [MasterUserController::class, 'toggle']
    )->name('master-user.toggle');

    // Context Hub
    Route::post(
        '/context-hub',
        [ContexHubController::class, 'change']
    )->name('context.hub');

});
