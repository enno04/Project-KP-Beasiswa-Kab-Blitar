<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Auto-Verifikasi Rekomendasi (SLA 3 Hari)
Schedule::command('beasiswa:auto-verify')->dailyAt('00:00');
