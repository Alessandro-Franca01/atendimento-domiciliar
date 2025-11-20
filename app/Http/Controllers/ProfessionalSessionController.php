<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Patient;
use App\Models\SessionSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfessionalSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:professional');
    }

    public function index()
    {
        $professional = Auth::guard('professional')->user();
        $sessions = Session::with(['patient', 'sessionSchedules'])
            ->where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professional.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $professional = Auth::guard('professional')->user();
        $patients = Patient::whereHas('sessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->orWhereDoesntHave('sessions')->get();

        return view('professional.sessions.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_sessao' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0|max:100',
            'forma_pagamento' => 'required|in:dinheiro,cartao,transferencia',
            'observacoes' => 'nullable|string',
            'horarios' => 'required|array|min:1',
            'horarios.*.dia_semana' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'horarios.*.hora_inicio' => 'required|date_format:H:i',
            'horarios.*.hora_fim' => 'required|date_format:H:i|after:horarios.*.hora_inicio',
        ]);

        // Calcular valor com desconto
        $valorComDesconto = $request->valor_sessao * (1 - ($request->desconto ?? 0) / 100);
        $valorTotal = $valorComDesconto * $request->total_sessoes;

        $session = Session::create([
            'patient_id' => $request->patient_id,
            'professional_id' => $professional->id,
            'descricao' => $request->descricao,
            'total_sessoes' => $request->total_sessoes,
            'sessoes_realizadas' => 0,
            'valor_sessao' => $request->valor_sessao,
            'desconto' => $request->desconto ?? 0,
            'valor_total' => $valorTotal,
            'forma_pagamento' => $request->forma_pagamento,
            'observacoes' => $request->observacoes,
            'status' => 'ativo',
        ]);

        // Criar horários
        foreach ($request->horarios as $horario) {
            SessionSchedule::create([
                'session_id' => $session->id,
                'dia_semana' => $horario['dia_semana'],
                'hora_inicio' => $horario['hora_inicio'],
                'hora_fim' => $horario['hora_fim'],
                'ativo' => true,
            ]);
        }

        return redirect()->route('professional.sessions.index')
            ->with('success', 'Sessão criada com sucesso!');
    }

    public function show(Session $session)
    {
        $professional = Auth::guard('professional')->user();
        
        // Verificar se a sessão pertence ao profissional
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $session->load(['patient', 'sessionSchedules', 'appointments']);

        return view('professional.sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $patients = Patient::whereHas('sessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->orWhereDoesntHave('sessions')->get();

        return view('professional.sessions.edit', compact('session', 'patients'));
    }

    public function update(Request $request, Session $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_sessao' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0|max:100',
            'forma_pagamento' => 'required|in:dinheiro,cartao,transferencia',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:ativo,inativo,concluido',
        ]);

        // Recalcular valor total se houver mudanças
        $valorComDesconto = $request->valor_sessao * (1 - ($request->desconto ?? 0) / 100);
        $valorTotal = $valorComDesconto * $session->total_sessoes;

        $session->update([
            'descricao' => $request->descricao,
            'valor_sessao' => $request->valor_sessao,
            'desconto' => $request->desconto ?? 0,
            'valor_total' => $valorTotal,
            'forma_pagamento' => $request->forma_pagamento,
            'observacoes' => $request->observacoes,
            'status' => $request->status,
        ]);

        return redirect()->route('professional.sessions.show', $session)
            ->with('success', 'Sessão atualizada com sucesso!');
    }

    public function destroy(Session $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        if ($session->appointments()->exists()) {
            return redirect()->route('professional.sessions.index')
                ->with('error', 'Não é possível excluir uma sessão que possui agendamentos.');
        }

        $session->delete();

        return redirect()->route('professional.sessions.index')
            ->with('success', 'Sessão excluída com sucesso!');
    }

    public function generateAppointments(Session $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $appointmentsCreated = 0;
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays(30)->endOfDay();

        $sessionSchedules = $session->sessionSchedules()->where('ativo', true)->get();

        foreach ($sessionSchedules as $schedule) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                if ($currentDate->format('l') === $this->getDayName($schedule->dia_semana)) {
                    $appointmentDateTime = $currentDate->copy()->setTimeFromTimeString($schedule->hora_inicio);
                    
                    // Verificar se já existe agendamento para este horário
                    $existingAppointment = Appointment::where('session_id', $session->id)
                        ->where('data_hora_inicio', $appointmentDateTime)
                        ->exists();

                    if (!$existingAppointment) {
                        Appointment::create([
                            'session_id' => $session->id,
                            'patient_id' => $session->patient_id,
                            'address_id' => $session->patient->addresses()->first()->id ?? null,
                            'data_hora_inicio' => $appointmentDateTime,
                            'data_hora_fim' => $currentDate->copy()->setTimeFromTimeString($schedule->hora_fim),
                            'status' => 'agendado',
                            'observacoes' => 'Agendamento automático',
                        ]);
                        
                        $appointmentsCreated++;
                    }
                }
                
                $currentDate->addDay();
            }
        }

        return redirect()->route('professional.sessions.show', $session)
            ->with('success', "{$appointmentsCreated} agendamentos criados com sucesso!");
    }

    private function getDayName($diaPortugues)
    {
        $days = [
            'segunda' => 'Monday',
            'terca' => 'Tuesday',
            'quarta' => 'Wednesday',
            'quinta' => 'Thursday',
            'sexta' => 'Friday',
            'sabado' => 'Saturday',
            'domingo' => 'Sunday',
        ];

        return $days[$diaPortugues] ?? 'Monday';
    }
}