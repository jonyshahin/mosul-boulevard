<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('inspection-requests:check-overdue')
    ->dailyAt(config('inspection_requests.overdue_check_time'))
    ->timezone(config('inspection_requests.overdue_timezone'))
    ->onOneServer()
    ->name('inspection-requests:check-overdue');
