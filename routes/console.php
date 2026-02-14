<?php

use App\Jobs\CheckSlaBreachJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// In production, this should be run via cron
Schedule::job(new CheckSlaBreachJob)->everyThirtySeconds();
