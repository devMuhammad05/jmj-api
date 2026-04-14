<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:expire')->daily();
Schedule::command('notifications:subscription-expiring')->dailyAt('09:00');
Schedule::command('notifications:trading-class-reminders')->everyMinute();
