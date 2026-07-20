<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoogleSheetController;

Route::post('/suite/import', [GoogleSheetController::class, 'importSuite']);
Route::post('/tracking/import', [GoogleSheetController::class, 'importTracking']);