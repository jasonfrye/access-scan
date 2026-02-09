<?php

use App\Http\Controllers\Api\V1\ScanApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| API Version 1 - Rate limited and authenticated
|
*/

// API v1 group with rate limiting and authentication
Route::prefix('v1')->middleware([
    'throttle:api',
    'auth:sanctum',
])->group(function () {
    // Scans
    Route::get('/scans', [ScanApiController::class, 'index'])->name('api.v1.scans.index');
    Route::post('/scans', [ScanApiController::class, 'store'])->name('api.v1.scans.store');
    Route::get('/scans/{scan}', [ScanApiController::class, 'show'])->name('api.v1.scans.show');
    Route::get('/scans/{scan}/status', [ScanApiController::class, 'status'])->name('api.v1.scans.status');
});

/*
|--------------------------------------------------------------------------
| API Routes (Legacy/Guest)
|--------------------------------------------------------------------------
|
| Guest scan endpoints - rate limited by IP
|
*/

Route::middleware('throttle:guest-scan')->group(function () {
    Route::post('/scans', [ScanApiController::class, 'store'])->name('api.scans.store');
    Route::get('/scans/{scan}', [ScanApiController::class, 'show'])->name('api.scans.show');
    Route::get('/scans/{scan}/status', [ScanApiController::class, 'status'])->name('api.scans.status');
});
