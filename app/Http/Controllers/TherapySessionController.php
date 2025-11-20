<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Professional;
use App\Models\TherapySession;
use App\Models\SessionSchedule;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class TherapySessionController extends Controller
{
    public function index()
    {
        $sessions = TherapySession::with(['patient', 'professional'])
            ->orderBy('data_inicio', 'desc')
            ->paginate(10);

        return view('therapy_sessions.index', compact('sessions'));
    }

    public function create()
    {
        $patients = Patient::where('status', 'ativo')->orderBy('nome')->get();
        $professionals = Professional::where('status', 'ativo')->orderBy('nome')->get();

        return view('therapy_sessions.create', compact('patients', 'professionals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'desconto_valor' => 'nullable|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $dados = $request->all();
        if (!empty($dados['valor_por_sessao'])) {
            $bruto = (float) $dados['total_sessoes'] * (float) $dados['valor_por_sessao'];
            if (!empty($dados['desconto_valor']) && (float) $dados['desconto_valor'] > 0) {
                $dados['valor_total'] = max(0, round($bruto - (float) $dados['desconto_valor'], 2));
            } else {
                $desconto = (float) ($dados['desconto_percentual'] ?? 0);
                $fator = max(0, min(100, $desconto));
                $dados['valor_total'] = round($bruto * (1 - ($fator / 100)), 2);
            }
        }

        $session = TherapySession::create($dados);

        return redirect()->route('therapy_sessions.show', $session)
            ->with('success', 'Therapy session created successfully.');
    }

    public function show(TherapySession $therapySession)
    {
        $therapySession->load(['patient', 'professional', 'sessionSchedules.address', 'appointments']);
        return view('therapy_sessions.show', compact('therapySession'));
    }

    public function edit(TherapySession $therapySession)
    {
        if ($therapySession->status === 'concluido') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Cannot edit a completed session.');
        }

        $patients = Patient::where('status', 'ativo')->orderBy('nome')->get();
        $professionals = Professional::where('status', 'ativo')->orderBy('nome')->get();

        return view('therapy_sessions.edit', compact('therapySession', 'patients', 'professionals'));
    }

    public function update(Request $request, TherapySession $therapySession)
    {
        if ($therapySession->status === 'concluido') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Cannot edit a completed session.');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'desconto_valor' => 'nullable|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:ativo,concluido,suspenso',
        ]);

        $dados = $request->all();
        if (!empty($dados['valor_por_sessao'])) {
            $bruto = (float) $dados['total_sessoes'] * (float) $dados['valor_por_sessao'];
            if (!empty($dados['desconto_valor']) && (float) $dados['desconto_valor'] > 0) {
                $dados['valor_total'] = max(0, round($bruto - (float) $dados['desconto_valor'], 2));
            } else {
                $desconto = (float) ($dados['desconto_percentual'] ?? 0);
                $fator = max(0, min(100, $desconto));
                $dados['valor_total'] = round($bruto * (1 - ($fator / 100)), 2);
            }
        }

        $therapySession->update($dados);

        return redirect()->route('therapy_sessions.show', $therapySession)
            ->with('success', 'Therapy session updated successfully.');
    }

    public function destroy(TherapySession $therapySession)
    {
        if ($therapySession->appointments()->exists()) {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Cannot delete session with appointments.');
        }

        $therapySession->delete();

        return redirect()->route('therapy_sessions.index')
            ->with('success', 'Therapy session deleted successfully.');
    }

    public function generateAppointments(TherapySession $therapySession, Request $request)
    {
        if ($therapySession->status !== 'ativo') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Can only generate appointments for active sessions.');
        }

        $request->validate([
            'dias' => 'required|integer|min:1|max:365',
        ]);

        $appointmentService = new AppointmentService();
        $appointmentsCreated = $appointmentService->generateAutomaticAppointments($therapySession, $request->dias);

        return redirect()->route('therapy_sessions.show', $therapySession)
            ->with('success', "{$appointmentsCreated} automatic appointments were created.");
    }
}