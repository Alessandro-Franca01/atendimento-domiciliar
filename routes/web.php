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
use App\Http\Controllers\ProfessionalAuthController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\ProfessionalSessionController;
use App\Http\Controllers\ProfessionalPatientController;
use App\Http\Controllers\ProfessionalAppointmentController;
use App\Http\Controllers\ProfessionalFinancialController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Autenticação de Profissionais
Route::prefix('professional')->name('professional.')->group(function () {
    // Login e Registro
    Route::get('/login', [ProfessionalAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ProfessionalAuthController::class, 'login']);
    Route::get('/register', [ProfessionalAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ProfessionalAuthController::class, 'register']);
    Route::post('/logout', [ProfessionalAuthController::class, 'logout'])->name('logout');

    // Área Autenticada do Profissional
    Route::middleware('auth:professional')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');
        
        // Perfil
        Route::get('/profile', [ProfessionalDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [ProfessionalDashboardController::class, 'updateProfile'])->name('profile.update');

        // Sessões
        Route::resource('sessions', ProfessionalSessionController::class)->names([
            'index' => 'sessions.index',
            'create' => 'sessions.create',
            'store' => 'sessions.store',
            'show' => 'sessions.show',
            'edit' => 'sessions.edit',
            'update' => 'sessions.update',
            'destroy' => 'sessions.destroy',
        ]);
        Route::post('/sessions/{session}/generate-appointments', [ProfessionalSessionController::class, 'generateAppointments'])
            ->name('sessions.generate-appointments');

        // Pacientes
        Route::resource('patients', ProfessionalPatientController::class)->names([
            'index' => 'patients.index',
            'create' => 'patients.create',
            'store' => 'patients.store',
            'show' => 'patients.show',
            'edit' => 'patients.edit',
            'update' => 'patients.update',
        ]);
        Route::post('/patients/{patient}/addresses', [ProfessionalPatientController::class, 'addAddress'])
            ->name('patients.add-address');

        // Agendamentos
        Route::resource('appointments', ProfessionalAppointmentController::class)->names([
            'index' => 'appointments.index',
            'show' => 'appointments.show',
            'edit' => 'appointments.edit',
            'update' => 'appointments.update',
        ]);
        Route::get('/appointments/calendar', [ProfessionalAppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::get('/appointments/today', [ProfessionalAppointmentController::class, 'today'])->name('appointments.today');
        Route::get('/appointments/week', [ProfessionalAppointmentController::class, 'week'])->name('appointments.week');
        Route::post('/appointments/{appointment}/confirm', [ProfessionalAppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('/appointments/{appointment}/cancel', [ProfessionalAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::get('/appointments/{appointment}/create-attendance', [ProfessionalAppointmentController::class, 'createAttendance'])
            ->name('appointments.create-attendance');

        // Financeiro
        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('/', [ProfessionalFinancialController::class, 'dashboard'])->name('dashboard');
            Route::get('/payments', [ProfessionalFinancialController::class, 'payments'])->name('payments');
            Route::post('/payments', [ProfessionalFinancialController::class, 'createPayment'])->name('payments.create');
            Route::get('/invoices', [ProfessionalFinancialController::class, 'invoices'])->name('invoices');
            Route::get('/monthly-report', [ProfessionalFinancialController::class, 'monthlyReport'])->name('monthly-report');
        });
    });
});

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
