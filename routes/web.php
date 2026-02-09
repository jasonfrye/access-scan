<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pricing', [BillingController::class, 'pricing'])->name('billing.pricing');
Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
Route::post('/scan', [ScanController::class, 'store'])->name('scans.store');
Route::get('/scan/{scan}/pending', [ScanController::class, 'pending'])->name('scan.pending');
Route::get('/scan/{scan}', [ScanController::class, 'results'])->name('scan.results');
Route::post('/scan/{scan}/email', [ScanController::class, 'captureEmail'])->name('scan.email');
Route::get('/scan/{scan}/status', [ScanController::class, 'status'])->name('scan.status');
Route::post('/scan/{scan}/cancel', [ScanController::class, 'cancel'])->name('scan.cancel');

// Stripe webhook (no auth required)
Route::post('/stripe/webhook', [BillingController::class, 'webhook'])->name('billing.webhook');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/scan', [DashboardController::class, 'storeScan'])->name('dashboard.scan.store');
    Route::get('/dashboard/scan/{scan}', [DashboardController::class, 'showScan'])->name('dashboard.scan');
    
    // Scheduled scans
    Route::post('/dashboard/scheduled-scans', [DashboardController::class, 'storeScheduledScan'])->name('dashboard.scheduled.store');
    Route::post('/dashboard/scheduled-scans/{schedule}/toggle', [DashboardController::class, 'toggleScheduledScan'])->name('dashboard.scheduled.toggle');
    Route::delete('/dashboard/scheduled-scans/{schedule}', [DashboardController::class, 'destroyScheduledScan'])->name('dashboard.scheduled.destroy');
    
    // Report downloads
    Route::get('/dashboard/scan/{scan}/export/pdf', [ReportController::class, 'pdf'])->name('report.pdf');
    Route::get('/dashboard/scan/{scan}/export/csv', [ReportController::class, 'csv'])->name('report.csv');
    Route::get('/dashboard/scan/{scan}/export/json', [ReportController::class, 'json'])->name('report.json');
    
    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/cancel', [BillingController::class, 'cancelSubscription'])->name('billing.cancel');
    Route::post('/billing/resume', [BillingController::class, 'resumeSubscription'])->name('billing.resume');
    Route::get('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
    Route::get('/billing/invoice/{id}', [BillingController::class, 'invoice'])->name('billing.invoice');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
