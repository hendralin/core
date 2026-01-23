<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule license expiration checks
Schedule::command('license:check-expiration')
    ->dailyAt('01:00') // Run daily at 1 AM
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping() // Prevent overlapping runs
    ->runInBackground(); // Run in background

// Optional: Also check at other times for more frequent monitoring
// Schedule::command('license:check-expiration')
//     ->hourly() // Run hourly for more frequent checks
//     ->between('8:00', '18:00') // Only during business hours
//     ->timezone('Asia/Jakarta')
//     ->withoutOverlapping()
//     ->runInBackground();

// Weekly license monitoring summary (optional)
// Schedule::command('license:check-expiration --days=90')
//     ->weeklyOn(1, '09:00') // Every Monday at 9 AM
//     ->withoutOverlapping()
//     ->runInBackground();

// Schedule IDX stock data scraping
Schedule::command('stock:scrape-idx')
    ->dailyAt('17:35') // Run daily at 17:35 WIB (5:35 PM)
    ->weekdays()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping() // Prevent overlapping runs
    ->runInBackground(); // Run in background

// Schedule combined stock prices fetching and breakthrough analysis
// Session 1: 09:00 - 12:00 WIB (02:00 - 05:00 UTC)
Schedule::command('stock:fetch-and-analyze-breakthroughs')
    ->everyTenMinutes()
    ->weekdays()
    ->timezone('Asia/Jakarta')
    ->between('09:00', '12:10')
    ->withoutOverlapping()
    ->runInBackground();

// Session 2: 13:30 - 16:00 WIB (06:30 - 09:00 UTC)
Schedule::command('stock:fetch-and-analyze-breakthroughs')
    ->everyTenMinutes()
    ->weekdays()
    ->timezone('Asia/Jakarta')
    ->between('13:30', '16:10')
    ->withoutOverlapping()
    ->runInBackground();
