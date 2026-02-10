<?php

use App\Http\Middleware\CheckPlanFeature;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'plan.feature' => CheckPlanFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Trial reminders - daily at 9am
        $schedule->command('access-scan:send-trial-reminders --process-expired')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/trial-reminders.log'))
            ->onSuccess(function () {
                //
            });

        // Scheduled scans - every 5 minutes
        $schedule->command('app:run-scheduled-scans')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scheduled-scans.log'));

        // Weekly digests - every Monday at 10am
        $schedule->command('access-scan:send-weekly-digests')
            ->weeklyOn(Schedule::MONDAY, '10:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/weekly-digests.log'));
    })
    ->create();
