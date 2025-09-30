<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Definición de un comando sencillo
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 📅 Programación de tareas
Schedule::command('worklogs:notify-absences')->dailyAt('08:00');
Schedule::command('inspire')->everyMinute();
Schedule::command('worklogs:send-business-day')->weekdays('monday')->at('09:00');
