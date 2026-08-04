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
use App\Http\Controllers\MonitoringDashboardController;
use Illuminate\Http\Request;

Route::get(
    '/monitoring/dashboard',
    [MonitoringDashboardController::class, 'index']
)->name('monitoring.dashboard');

Route::get(
    '/monitoring/live',
    [MonitoringDashboardController::class,'live']
)->name('monitoring.live');

Route::get('/dashboard/live', [DashboardController::class, 'live'])
    ->name('dashboard.live');
Route::get(
    '/monitoring-std/live',
    [MonitoringStdController::class, 'live']
)->name('monitoring.live');
Route::get('/ping', function (Request $request) {

    if ($request->header('X-API-KEY') !== env('API_KEY')) {
        return response()->json([
            'status' => 'unauthorized'
        ], 401);
    }

    return response()->json([
        'status' => 'ok'
    ]);
});

    Route::post('/suite/import', [SuiteController::class,'import'])
        ->name('suite.import');

    Route::post('/tracking/import', [TrackingController::class,'import'])
    ->name('tracking.import');



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

    Route::post('/monitoring-std/import',[MonitoringStdController::class,'import'])
        ->name('monitoring.import');

    Route::get('/tracking', [TrackingController::class,'index'])
        ->name('tracking.index');



    Route::get('/performance/kurir',[DashboardController::class,'kurirPerformance'])
        ->name('performance.index');

    Route::get('/monitoring-std',[MonitoringStdController::class,'index'])
        ->name('monitoring.index');



    Route::delete('/monitoring-std/reset',[MonitoringStdController::class,'reset'])
        ->name('monitoring.reset');

    Route::get('/monitoring-std/driver/{driverId}',
        [MonitoringStdController::class,'driverDetail'])
        ->name('monitoring.driver');
        Route::get(
    '/monitoring/driver/{driverId}',
    [MonitoringDashboardController::class,'driver']
)->name('monitoring.driver');

    Route::get('/home',[App\Http\Controllers\HomeController::class,'index'])
        ->name('home');

});

Route::middleware(['auth','active','role:owner'])->group(function () {

  // Master Hub
    Route::resource('hub', HubController::class);

    // Master User
    Route::resource('master-user', MasterUserController::class)
    ->parameters([
        'master-user' => 'user'
    ]);

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
