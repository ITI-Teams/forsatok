<?php

use App\Domains\Jobs\Services\JobDeadlineDetection;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('jobs:check-deadlines', function (JobDeadlineDetection $service) {
    $this->info('Checking job deadlines...');
    $count = $service->detectAll();
    $this->info("Done. {$count} jobs expired.");
})->purpose('Check for expired jobs and deactivate them');

// Schedule the command
Schedule::command('jobs:check-deadlines')->everySixHours()->withoutOverlapping();
