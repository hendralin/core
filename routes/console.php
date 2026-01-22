<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule license expiration checks
Schedule::command('license:check-expiration')
    ->dailyAt('09:00') // Run daily at 9 AM
    ->withoutOverlapping() // Prevent overlapping runs
    ->runInBackground(); // Run in background

// Optional: Also check at other times for more frequent monitoring
Schedule::command('license:check-expiration')
    ->hourly() // Run hourly for more frequent checks
    ->between('8:00', '18:00') // Only during business hours
    ->withoutOverlapping()
    ->runInBackground();

// Weekly license monitoring summary (optional)
// Schedule::command('license:check-expiration --days=90')
//     ->weeklyOn(1, '09:00') // Every Monday at 9 AM
//     ->withoutOverlapping()
//     ->runInBackground();

// Schedule IDX stock data scraping
Schedule::command('stock:scrape-idx')
    ->dailyAt('17:35') // Run daily at 17:35 (5:35 PM)
    ->withoutOverlapping() // Prevent overlapping runs
    ->runInBackground(); // Run in background

// Schedule combined stock prices fetching and breakthrough analysis
Schedule::command('stock:fetch-and-analyze-breakthroughs')
    ->everyTenMinutes() // Run every 10 minutes
    ->withoutOverlapping() // Prevent overlapping runs
    ->runInBackground(); // Run in background
