<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification de notre tâche
// ->twiceMonthly(1, 15, '08:00') signifie : Le 1er et le 15 de chaque mois, à 8h00 du matin.
Schedule::command('app:send-planning-reminder')->twiceMonthly(1, 15, '08:00');