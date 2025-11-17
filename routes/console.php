<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendar geração automática de agendamentos diariamente às 6h da manhã
Schedule::command('agendamentos:gerar-automaticos --dias=30')->dailyAt('06:00')
    ->name('Gerar Agendamentos Automáticos')
    ->withoutOverlapping();
