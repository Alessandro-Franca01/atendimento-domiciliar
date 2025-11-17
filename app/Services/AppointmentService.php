<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Session;
use App\Models\SessionSchedule;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Gera agendamentos automáticos baseados nos horários fixos das sessões
     */
    public function gerarAgendamentosAutomaticos($dias = 30)
    {
        $appointmentsCreated = 0;
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($dias)->endOfDay();

        // Buscar todas as sessões ativas com horários fixos
        $sessions = Session::where('status', 'ativo')
            ->whereHas('sessionSchedules', function ($query) {
                $query->where('ativo', true);
            })
            ->with(['sessionSchedules' => function ($query) {
                $query->where('ativo', true);
            }])
            ->get();

        foreach ($sessions as $session) {
            foreach ($session->sessionSchedules as $fixedSchedule) {
                $appointmentsCreated += $this->criarAgendamentosParaHorarioFixo(
                    $session,
                    $fixedSchedule,
                    $startDate,
                    $endDate
                );
            }
        }

        return $appointmentsCreated;
    }

    /**
     * Cria agendamentos para um horário fixo específico dentro de um período
     */
    private function criarAgendamentosParaHorarioFixo(Session $session, SessionSchedule $fixedSchedule, Carbon $startDate, Carbon $endDate)
    {
        $appointmentsCreated = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Verificar se o dia da semana corresponde ao horário fixo
            if ($currentDate->dayOfWeek === ($fixedSchedule->dia_da_semana - 1)) {
                // Criar data/hora do agendamento
                $appointmentDateTime = $currentDate->copy()->setTimeFromTimeString($fixedSchedule->hora);
                
                // Verificar se já existe um agendamento para este horário
                $existingAppointment = Appointment::where('session_id', $session->id)
                    ->where('session_schedule_id', $fixedSchedule->id)
                    ->where('data_hora_inicio', $appointmentDateTime)
                    ->exists();

                if (!$existingAppointment) {
                    // Criar o agendamento
                    Appointment::create([
                        'session_id' => $session->id,
                        'session_schedule_id' => $fixedSchedule->id,
                        'patient_id' => $session->patient_id,
                        'address_id' => $fixedSchedule->address_id,
                        'professional_id' => $session->professional_id,
                        'data_hora_inicio' => $appointmentDateTime,
                        'data_hora_fim' => $appointmentDateTime->copy()->addMinutes($fixedSchedule->duracao_minutos),
                        'status' => 'agendado',
                        'observacoes' => 'Agendamento automático',
                    ]);

                    $appointmentsCreated++;
                }
            }

            $currentDate->addDay();
        }

        return $appointmentsCreated;
    }
}