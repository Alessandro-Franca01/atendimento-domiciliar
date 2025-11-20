<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\TherapySession;
use App\Models\SessionSchedule;
use Illuminate\Http\Request;

class SessionScheduleController extends Controller
{
    public function store(Request $request, TherapySession $therapySession)
    {
        if ($therapySession->status === 'concluido') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Não é possível adicionar horários a uma sessão concluída.');
        }

        $request->validate([
            'dia_da_semana' => 'required|integer|between:1,7',
            'hora' => 'required|date_format:H:i',
            'duracao_minutos' => 'required|integer|min:30|max:240',
            'address_id' => 'required|exists:addresses,id',
        ]);

        // Verificar se o endereço pertence ao paciente da sessão
        $address = Address::find($request->address_id);
        if ($address->patient_id !== $therapySession->patient_id) {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'O endereço não pertence ao paciente da sessão.');
        }

        $therapySession->sessionSchedules()->create($request->all());

        return redirect()->route('therapy_sessions.show', $therapySession)
            ->with('success', 'Horário fixo adicionado com sucesso.');
    }

    public function update(Request $request, TherapySession $therapySession, SessionSchedule $sessionSchedule)
    {
        if ($therapySession->status === 'concluido') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Não é possível editar horários de uma sessão concluída.');
        }

        $request->validate([
            'dia_da_semana' => 'required|integer|between:1,7',
            'hora' => 'required|date_format:H:i',
            'duracao_minutos' => 'required|integer|min:30|max:240',
            'address_id' => 'required|exists:addresses,id',
            'ativo' => 'boolean',
        ]);

        // Verificar se o endereço pertence ao paciente da sessão
        $address = Address::find($request->address_id);
        if ($address->patient_id !== $therapySession->patient_id) {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'O endereço não pertence ao paciente da sessão.');
        }

        $sessionSchedule->update($request->all());

        return redirect()->route('therapy_sessions.show', $therapySession)
            ->with('success', 'Horário fixo atualizado com sucesso.');
    }

    public function destroy(TherapySession $therapySession, SessionSchedule $sessionSchedule)
    {
        if ($therapySession->status === 'concluido') {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Não é possível excluir horários de uma sessão concluída.');
        }

        if ($sessionSchedule->appointments()->exists()) {
            return redirect()->route('therapy_sessions.show', $therapySession)
                ->with('error', 'Não é possível excluir horário com agendamentos.');
        }

        $sessionSchedule->delete();

        return redirect()->route('therapy_sessions.show', $therapySession)
            ->with('success', 'Horário fixo excluído com sucesso.');
    }
}