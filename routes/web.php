<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailPreferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', [ScanController::class, 'index'])->name('home');

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
    Route::get('/dashboard/scan/{scan}/page/{scanPage}', [DashboardController::class, 'showScanPage'])->name('dashboard.scan.page');
    Route::post('/dashboard/scan/{scan}/retry', [DashboardController::class, 'retryScan'])->name('dashboard.scan.retry');

    // Scheduled scans (paid feature)
    Route::post('/dashboard/scheduled-scans', [DashboardController::class, 'storeScheduledScan'])
        ->name('dashboard.scheduled.store')
        ->middleware('plan.feature:scheduled_scans');
    Route::post('/dashboard/scheduled-scans/{schedule}/toggle', [DashboardController::class, 'toggleScheduledScan'])
        ->name('dashboard.scheduled.toggle')
        ->middleware('plan.feature:scheduled_scans');
    Route::delete('/dashboard/scheduled-scans/{schedule}', [DashboardController::class, 'destroyScheduledScan'])
        ->name('dashboard.scheduled.destroy')
        ->middleware('plan.feature:scheduled_scans');

    // Report downloads (paid feature)
    Route::get('/dashboard/scan/{scan}/export/pdf', [ReportController::class, 'pdf'])
        ->name('report.pdf')
        ->middleware('plan.feature:pdf_export');
    Route::get('/dashboard/scan/{scan}/export/csv', [ReportController::class, 'csv'])
        ->name('report.csv')
        ->middleware('plan.feature:csv_export');
    Route::get('/dashboard/scan/{scan}/export/json', [ReportController::class, 'json'])
        ->name('report.json')
        ->middleware('plan.feature:json_export');

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

// Email unsubscribe (signed URL, no auth required)
Route::get('/email/unsubscribe/{user}', [EmailPreferenceController::class, 'show'])->name('email.unsubscribe');
Route::post('/email/unsubscribe/{user}', [EmailPreferenceController::class, 'update'])->name('email.unsubscribe.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/email-preferences', [ProfileController::class, 'updateEmailPreferences'])->name('profile.email-preferences.update');
    Route::post('/profile/api-key/create', [ProfileController::class, 'createApiKey'])->name('profile.api-key.create');
    Route::delete('/profile/api-key/revoke', [ProfileController::class, 'revokeApiKey'])->name('profile.api-key.revoke');
});

Route::get('/api/docs', function () {
    return view('api.docs');
})->name('api.docs');

Route::get('/privacy', fn () => view('legal.privacy'))->name('privacy');
Route::get('/terms', fn () => view('legal.terms'))->name('terms');

// Landing pages for ad campaigns
Route::get('/lp/small-business', fn () => view('landing.small-business'))->name('landing.small-business');
Route::get('/lp/agencies', fn () => view('landing.agencies'))->name('landing.agencies');

// Test pages for scanner validation (static pages with intentional accessibility issues)
Route::get('/test/good', fn () => view('test-pages.good'))->name('test.good');
Route::get('/test/mediocre', fn () => view('test-pages.mediocre'))->name('test.mediocre');
Route::get('/test/poor', fn () => view('test-pages.poor'))->name('test.poor');

// Mailable previews (local environment only)
Route::get('/mailable/{type}', function (string $type) {
    abort_unless(App::environment('local'), 404);

    $user = \App\Models\User::factory()->make(['id' => 1]);
    $scan = \App\Models\Scan::factory()->make(['score' => 78, 'grade' => 'C', 'pages_scanned' => 5, 'issues_found' => 12]);
    $previousScan = \App\Models\Scan::factory()->make(['score' => 65, 'grade' => 'D', 'pages_scanned' => 5, 'issues_found' => 20]);

    return match ($type) {
        'welcome' => new \App\Mail\WelcomeMail($user),
        'scan-complete' => new \App\Mail\ScanCompleteMail($scan),
        'payment-failed' => new \App\Mail\PaymentFailedMail($user, 'Pro', '$29.00', '4242', 'Card declined', '/billing'),
        'trial-expiring' => new \App\Mail\TrialExpiringMail($user, 3),
        'trial-expired' => new \App\Mail\TrialExpiredMail($user),
        're-engagement' => new \App\Mail\ReEngagementMail($user, 14),
        'plan-benefit' => new \App\Mail\PlanBenefitMail($user, ['Scheduled Scans', 'PDF Reports', 'API Access']),
        'score-improve' => new \App\Mail\ScoreImproveMail($user, $scan, $previousScan, 13),
        'weekly-digest' => new \App\Mail\WeeklyDigestMail($user, [
            'scans_run' => 5,
            'pages_scanned' => 23,
            'issues_found' => 47,
            'issues_fixed' => 12,
            'average_score' => 74,
        ]),
        'regression-alert' => new \App\Mail\RegressionAlertMail($scan, $previousScan, 13),
        'first-issue-fix' => new \App\Mail\FirstIssueFixMail($user, $scan, [
            ['code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37', 'count' => 5],
            ['code' => 'WCAG2AA.Principle4.Guideline4_1.4_1_2.H91', 'count' => 3],
        ]),
        default => abort(404),
    };
})->name('mailable.preview');

require __DIR__.'/auth.php';
