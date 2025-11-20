<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TherapySession;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function cashFlow()
    {
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $daily = Payment::where('status', 'pago')
            ->whereBetween('data_pagamento', [$startOfMonth, $endOfMonth])
            ->selectRaw('date(data_pagamento) as dia, sum(valor) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $totalMonth = $daily->sum('total');

        $receivablesForecast = Invoice::where('status', 'aberta')
            ->whereBetween('data_vencimento', [$startOfMonth, $endOfMonth])
            ->sum('valor_total');

        return view('financeiro.reports.cash_flow', compact('daily', 'totalMonth', 'receivablesForecast'));
    }

    public function sessionsReport()
    {
        $sessions = TherapySession::withCount(['appointments'])->get();
        $paid = $sessions->filter(fn($s) => ($s->valor_total ?? 0) > 0 && abs(($s->valor_total) - ($s->valor_pago ?? 0)) < 0.00001)->count();
        $unpaid = $sessions->count() - $paid;
        $pendingAttendances = Attendance::where('status_pagamento', 'pendente')->count();
        return view('financeiro.reports.sessions', compact('sessions', 'paid', 'unpaid', 'pendingAttendances'));
    }

    public function patientsReport()
    {
        $defaulters = Invoice::where('status', 'vencida')->with('patient')->get();
        $paymentHistory = Payment::with('patient')->latest()->limit(50)->get();
        return view('financeiro.reports.patients', compact('defaulters', 'paymentHistory'));
    }

    public function generalReport()
    {
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $monthlyPayments = Payment::where('status', 'pago')
            ->whereBetween('data_pagamento', [$startOfMonth, $endOfMonth])
            ->get();
        $totalMonth = $monthlyPayments->sum('valor');
        $patientsInMonth = $monthlyPayments->pluck('patient_id')->unique()->count();
        $averageTicket = $patientsInMonth > 0 ? $totalMonth / $patientsInMonth : 0;

        $profitBySession = TherapySession::whereNotNull('valor_total')
            ->selectRaw('id, valor_total, valor_pago')
            ->get();

        return view('financeiro.reports.general', compact('totalMonth', 'averageTicket', 'profitBySession'));
    }
}