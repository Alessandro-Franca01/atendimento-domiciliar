<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\SessionScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\ProfessionalAuthController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\ProfessionalSessionController;
use App\Http\Controllers\ProfessionalPatientController;
use App\Http\Controllers\ProfessionalAppointmentController;
use App\Http\Controllers\ProfessionalFinancialController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Professional Authentication
Route::prefix('professional')->name('professional.')->group(function () {
    // Login and Register
    Route::get('/login', [ProfessionalAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ProfessionalAuthController::class, 'login']);
    Route::get('/register', [ProfessionalAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ProfessionalAuthController::class, 'register']);
    Route::post('/logout', [ProfessionalAuthController::class, 'logout'])->name('logout');

    // Authenticated Professional Area
    Route::middleware('auth:professional')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');
        
        // Profile
        Route::get('/profile', [ProfessionalDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [ProfessionalDashboardController::class, 'updateProfile'])->name('profile.update');

        // Therapy Sessions
        Route::resource('sessions', ProfessionalSessionController::class)->parameters([
            'sessions' => 'session'
        ]);

        // Patients
        Route::resource('patients', ProfessionalPatientController::class);
        Route::post('/patients/{patient}/addresses', [ProfessionalPatientController::class, 'addAddress'])
            ->name('patients.add-address');

        // Appointments
        Route::get('/appointments/calendar', [ProfessionalAppointmentController::class, 'calendar'])
            ->name('appointments.calendar');
        Route::get('/appointments/today', [ProfessionalAppointmentController::class, 'today'])
            ->name('appointments.today');
        Route::get('/appointments/week', [ProfessionalAppointmentController::class, 'week'])
            ->name('appointments.week');
        Route::post('/appointments/{appointment}/confirm', [ProfessionalAppointmentController::class, 'confirm'])
            ->name('appointments.confirm');
        Route::post('/appointments/{appointment}/cancel', [ProfessionalAppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        Route::get('/appointments/{appointment}/create-attendance', [ProfessionalAppointmentController::class, 'createAttendance'])
            ->name('appointments.create-attendance');
        Route::resource('appointments', ProfessionalAppointmentController::class)->only([
            'index', 'show', 'edit', 'update'
        ]);

        // Financial
        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('/', [ProfessionalFinancialController::class, 'dashboard'])->name('dashboard');
            Route::get('/payments', [ProfessionalFinancialController::class, 'payments'])->name('payments');
            Route::post('/payments', [ProfessionalFinancialController::class, 'createPayment'])->name('payments.create');
            Route::get('/invoices', [ProfessionalFinancialController::class, 'invoices'])->name('invoices');
            Route::get('/monthly-report', [ProfessionalFinancialController::class, 'monthlyReport'])->name('monthly-report');
        });
    });
});

// Admin/Management Routes (authenticated with web guard)
Route::middleware('auth')->group(function () {
    
    // Patients
    Route::resource('patients', PatientController::class);

    // Therapy Sessions
    Route::resource('therapy-sessions', TherapySessionController::class)->parameters([
        'therapy-sessions' => 'therapySession'
    ]);
    Route::post('/therapy-sessions/{therapySession}/generate-appointments', [TherapySessionController::class, 'generateAppointments'])
        ->name('therapy-sessions.generate-appointments');

    // Session Schedules (Fixed Schedules for Therapy Sessions)
    Route::post('/therapy-sessions/{therapySession}/schedules', [SessionScheduleController::class, 'store'])
        ->name('therapy-sessions.schedules.store');
    Route::put('/therapy-sessions/{therapySession}/schedules/{sessionSchedule}', [SessionScheduleController::class, 'update'])
        ->name('therapy-sessions.schedules.update');
    Route::delete('/therapy-sessions/{therapySession}/schedules/{sessionSchedule}', [SessionScheduleController::class, 'destroy'])
        ->name('therapy-sessions.schedules.destroy');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])
        ->name('appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');

    // Attendances
    Route::resource('attendances', AttendanceController::class);

    // Financial - Payments
    Route::resource('payments', PaymentController::class);

    // Financial - Invoices
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::get('/invoices/monthly/create', [InvoiceController::class, 'createMonthly'])
        ->name('invoices.monthly.create');
    Route::post('/invoices/monthly', [InvoiceController::class, 'storeMonthly'])
        ->name('invoices.monthly.store');

    // Financial - Reports
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/cash-flow', [FinancialReportController::class, 'cashFlow'])
            ->name('cash-flow');
        Route::get('/sessions-report', [FinancialReportController::class, 'sessionsReport'])
            ->name('sessions-report');
        Route::get('/patients-report', [FinancialReportController::class, 'patientsReport'])
            ->name('patients-report');
        Route::get('/general-report', [FinancialReportController::class, 'generalReport'])
            ->name('general-report');
    });
});

// Legacy routes redirect (optional - remove after full migration)
Route::redirect('/pacientes', '/patients')->name('pacientes.index');
Route::redirect('/pacientes/create', '/patients')->name('pacientes.create');
Route::redirect('/sessoes', '/therapy-sessions')->name('sessoes.index');
Route::redirect('/sessoes/create', '/therapy-sessions')->name('sessoes.create');
Route::redirect('/pagamentos', '/payments')->name('pagamentos.index');
Route::redirect('/agendamentos', '/appointments')->name('agendamentos.index');
Route::redirect('/faturas', '/invoices')->name('faturas.index');