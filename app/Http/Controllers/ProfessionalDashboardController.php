<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\TherapySession;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessionalDashboardController extends Controller
{
    public function index()
    {
        $professional = Auth::guard('professional')->user();
        
        // Estatísticas do profissional
        $totalPatients = Patient::whereHas('therapySessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->count();

        $totalSessions = TherapySession::where('professional_id', $professional->id)->count();
        $activeSessions = TherapySession::where('professional_id', $professional->id)
            ->where('status', 'ativo')
            ->count();

        $today = Carbon::today();
        $appointmentsToday = Appointment::whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereDate('data_hora_inicio', $today)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->count();

        // Agendamentos próximos
        $upcomingAppointments = Appointment::with(['patient', 'address', 'therapySession'])
            ->whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->where('data_hora_inicio', '>=', $today)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->limit(10)
            ->get();

        // Sessões próximas do fim
        $sessionsNearEnd = TherapySession::with('patient')
            ->where('professional_id', $professional->id)
            ->where('status', 'ativo')
            ->whereRaw('sessoes_realizadas >= total_sessoes - 2')
            ->orderBy('sessoes_realizadas', 'desc')
            ->limit(5)
            ->get();

        // Financeiro
        $monthlyRevenue = Payment::where('professional_id', $professional->id)
            ->whereMonth('data_pagamento', Carbon::now()->month)
            ->where('status', 'pago')
            ->sum('valor');

        $pendingPayments = Payment::where('professional_id', $professional->id)
            ->where('status', 'pendente')
            ->count();

        return view('professional.dashboard', compact(
            'professional',
            'totalPatients',
            'totalSessions',
            'activeSessions',
            'appointmentsToday',
            'upcomingAppointments',
            'sessionsNearEnd',
            'monthlyRevenue',
            'pendingPayments'
        ));
    }

    public function profile()
    {
        $professional = Auth::guard('professional')->user();
        return view('professional.profile', compact('professional'));
    }

    public function updateProfile(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'especialidades' => 'nullable|array',
            'horario_funcionamento' => 'nullable|string',
            'sobre' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('professionals', 'public');
            $data['foto'] = $path;
        }

        $professional->update($data);

        return redirect()->route('professional.profile')->with('success', 'Perfil atualizado com sucesso!');
    }
}