<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SessaoController;
use App\Http\Controllers\SessaoHorarioController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\RelatorioFinanceiroController;

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

// Financeiro - Pagamentos
Route::get('pagamentos', [PagamentoController::class, 'index'])->name('pagamentos.index');
Route::get('pagamentos/create', [PagamentoController::class, 'create'])->name('pagamentos.create');
Route::post('pagamentos', [PagamentoController::class, 'store'])->name('pagamentos.store');
Route::post('pagamentos/{pagamento}/estornar', [PagamentoController::class, 'estornar'])->name('pagamentos.estornar');

// Financeiro - Faturas
Route::get('faturas', [FaturaController::class, 'index'])->name('faturas.index');
Route::get('faturas/{fatura}', [FaturaController::class, 'show'])->name('faturas.show');
Route::get('faturas/mensal/create', [FaturaController::class, 'createMensal'])->name('faturas.mensal.create');
Route::post('faturas/mensal', [FaturaController::class, 'storeMensal'])->name('faturas.mensal.store');

// Financeiro - Relatórios
Route::get('financeiro/fluxo-caixa', [RelatorioFinanceiroController::class, 'fluxoCaixa'])->name('financeiro.fluxo-caixa');
Route::get('financeiro/relatorio-sessoes', [RelatorioFinanceiroController::class, 'relatorioSessoes'])->name('financeiro.relatorio-sessoes');
Route::get('financeiro/relatorio-pacientes', [RelatorioFinanceiroController::class, 'relatorioPacientes'])->name('financeiro.relatorio-pacientes');
Route::get('financeiro/relatorio-geral', [RelatorioFinanceiroController::class, 'relatorioGeral'])->name('financeiro.relatorio-geral');
