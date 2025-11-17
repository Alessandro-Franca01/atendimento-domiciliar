<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $totalSessions = Session::count();
        $activeSessions = Session::where('status', 'ativo')->count();
        
        $today = Carbon::today();
        $appointmentsToday = Appointment::whereDate('data_hora_inicio', $today)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->count();

        $upcomingAppointments = Appointment::with(['patient', 'address'])
            ->where('data_hora_inicio', '>=', $today)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->limit(10)
            ->get();

        $sessionsNearEnd = Session::with('patient')
            ->where('status', 'ativo')
            ->whereRaw('sessoes_realizadas >= total_sessoes - 2')
            ->orderBy('sessoes_realizadas', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPatients',
            'totalSessions',
            'activeSessions',
            'appointmentsToday',
            'upcomingAppointments',
            'sessionsNearEnd'
        ));
    }
}