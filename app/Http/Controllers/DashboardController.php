<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Paciente;
use App\Models\Sessao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPacientes = Paciente::count();
        $totalSessoes = Sessao::count();
        $sessoesAtivas = Sessao::where('status', 'ativo')->count();
        
        $hoje = Carbon::today();
        $agendamentosHoje = Agendamento::whereDate('data_hora_inicio', $hoje)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->count();

        $proximosAgendamentos = Agendamento::with(['paciente', 'endereco'])
            ->where('data_hora_inicio', '>=', $hoje)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->limit(10)
            ->get();

        $sessoesPertoDeTerminar = Sessao::with('paciente')
            ->where('status', 'ativo')
            ->whereRaw('sessoes_realizadas >= total_sessoes - 2')
            ->orderBy('sessoes_realizadas', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPacientes',
            'totalSessoes',
            'sessoesAtivas',
            'agendamentosHoje',
            'proximosAgendamentos',
            'sessoesPertoDeTerminar'
        ));
    }
}
