<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfessionalFinancialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:professional');
    }

    public function dashboard()
    {
        $professional = Auth::guard('professional')->user();
        
        // Estatísticas financeiras
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Receita do mês atual
        $monthlyRevenue = Payment::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereMonth('data_pagamento', $currentMonth)
            ->whereYear('data_pagamento', $currentYear)
            ->where('status', 'pago')
            ->sum('valor');

        // Receita do mês passado
        $lastMonthRevenue = Payment::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereMonth('data_pagamento', Carbon::now()->subMonth()->month)
            ->whereYear('data_pagamento', Carbon::now()->subMonth()->year)
            ->where('status', 'pago')
            ->sum('valor');

        // Pagamentos pendentes
        $pendingPayments = Payment::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->where('status', 'pendente')
            ->count();

        $pendingPaymentsValue = Payment::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->where('status', 'pendente')
            ->sum('valor');

        // Atendimentos do mês
        $monthlyAttendances = Attendance::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereMonth('data_atendimento', $currentMonth)
            ->whereYear('data_atendimento', $currentYear)
            ->count();

        // Evolução mensal (últimos 6 meses)
        $monthlyEvolution = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Payment::whereHas('appointment.session', function($query) use ($professional) {
                    $query->where('professional_id', $professional->id);
                })
                ->whereMonth('data_pagamento', $date->month)
                ->whereYear('data_pagamento', $date->year)
                ->where('status', 'pago')
                ->sum('valor');

            $monthlyEvolution[] = [
                'month' => $date->format('M/Y'),
                'revenue' => $revenue,
            ];
        }

        // Formas de pagamento mais utilizadas
        $paymentMethods = Payment::whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->where('status', 'pago')
            ->select('forma_pagamento', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as total_value'))
            ->groupBy('forma_pagamento')
            ->orderBy('total', 'desc')
            ->get();

        return view('professional.financial.dashboard', compact(
            'monthlyRevenue',
            'lastMonthRevenue',
            'pendingPayments',
            'pendingPaymentsValue',
            'monthlyAttendances',
            'monthlyEvolution',
            'paymentMethods'
        ));
    }

    public function payments(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $query = Payment::with(['appointment.patient', 'appointment.session'])
            ->whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            });

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('data_pagamento', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('data_pagamento', '<=', $request->date_end);
        }

        $payments = $query->orderBy('data_pagamento', 'desc')->paginate(15);

        return view('professional.financial.payments', compact('payments'));
    }

    public function createPayment(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'valor' => 'required|numeric|min:0',
            'forma_pagamento' => 'required|in:dinheiro,cartao,transferencia',
            'data_pagamento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        // Verificar se o agendamento pertence ao profissional
        $appointment = Attendance::find($request->appointment_id);
        if (!$appointment || $appointment->appointment->session->professional_id !== $professional->id) {
            return redirect()->back()->with('error', 'Agendamento inválido.');
        }

        // Verificar se já existe pagamento para este agendamento
        $existingPayment = Payment::where('appointment_id', $request->appointment_id)->first();
        if ($existingPayment) {
            return redirect()->back()->with('error', 'Já existe um pagamento registrado para este agendamento.');
        }

        Payment::create([
            'appointment_id' => $request->appointment_id,
            'valor' => $request->valor,
            'forma_pagamento' => $request->forma_pagamento,
            'data_pagamento' => $request->data_pagamento,
            'status' => 'pago',
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('professional.financial.payments')
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function invoices(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $query = Invoice::with(['patient', 'invoiceItems'])
            ->whereHas('patient.sessions', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            });

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('mes_referencia', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('mes_referencia', $request->year);
        }

        $invoices = $query->orderBy('mes_referencia', 'desc')->paginate(15);

        return view('professional.financial.invoices', compact('invoices'));
    }

    public function monthlyReport(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $month = $request->filled('month') ? $request->month : Carbon::now()->month;
        $year = $request->filled('year') ? $request->year : Carbon::now()->year;

        // Pagamentos do mês
        $payments = Payment::with(['appointment.patient', 'appointment.session'])
            ->whereHas('appointment.session', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereMonth('data_pagamento', $month)
            ->whereYear('data_pagamento', $year)
            ->orderBy('data_pagamento')
            ->get();

        // Resumo por paciente
        $summaryByPatient = $payments->groupBy('appointment.patient.nome')->map(function ($group) {
            return [
                'total' => $group->sum('valor'),
                'count' => $group->count(),
            ];
        });

        // Resumo por forma de pagamento
        $summaryByMethod = $payments->groupBy('forma_pagamento')->map(function ($group) {
            return [
                'total' => $group->sum('valor'),
                'count' => $group->count(),
            ];
        });

        $totalRevenue = $payments->where('status', 'pago')->sum('valor');
        $totalCount = $payments->count();

        return view('professional.financial.monthly-report', compact(
            'payments',
            'summaryByPatient',
            'summaryByMethod',
            'totalRevenue',
            'totalCount',
            'month',
            'year'
        ));
    }
}