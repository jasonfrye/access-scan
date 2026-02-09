<?php

use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('api')->group(function () {
    // Guest scan endpoints
    Route::post('/scans', [ScanController::class, 'store'])->name('api.scans.store');
    Route::get('/scans/{scan}', [ScanController::class, 'show'])->name('api.scans.show');
    Route::get('/scans/{scan}/status', [ScanController::class, 'status'])->name('api.scans.status');
});
