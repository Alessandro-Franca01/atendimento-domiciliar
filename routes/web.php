<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SessaoController;
use App\Http\Controllers\SessaoHorarioController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ProfissionalController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Pacientes
Route::resource('pacientes', PacienteController::class);

// Sessões
Route::resource('sessoes', SessaoController::class);
Route::post('/sessoes/{sessao}/gerar-agendamentos', [SessaoController::class, 'gerarAgendamentos'])
    ->name('sessoes.gerar-agendamentos');

// Horários Fixos das Sessões
Route::post('/sessoes/{sessao}/horarios', [SessaoHorarioController::class, 'store'])->name('sessoes.horarios.store');
Route::put('/sessoes/{sessao}/horarios/{sessaoHorario}', [SessaoHorarioController::class, 'update'])->name('sessoes.horarios.update');
Route::delete('/sessoes/{sessao}/horarios/{sessaoHorario}', [SessaoHorarioController::class, 'destroy'])->name('sessoes.horarios.destroy');

// Agendamentos
Route::resource('agendamentos', AgendamentoController::class);

// Atendimentos
Route::resource('atendimentos', AtendimentoController::class);

// Profissionais
Route::resource('profissionals', ProfissionalController::class);
