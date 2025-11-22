<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\TherapySession;
use App\Models\SessionSchedule;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Generate automatic appointments based on fixed session schedules
     */
    public function generateAutomaticAppointments(TherapySession $therapySession, int $days = 30): int
    {
        $appointmentsCreated = 0;
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($days)->endOfDay();

        $schedules = $therapySession->sessionSchedules()
            ->where('ativo', true)
            ->get();

        foreach ($schedules as $schedule) {
            $appointmentsCreated += $this->createAppointmentsForSchedule(
                $therapySession,
                $schedule,
                $startDate,
                $endDate
            );
        }

        return $appointmentsCreated;
    }

    /**
     * Generate automatic appointments for all active sessions
     */
    public function generateAllAutomaticAppointments(int $days = 30): int
    {
        $appointmentsCreated = 0;
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($days)->endOfDay();

        // Get all active sessions with active schedules
        $sessions = TherapySession::where('status', 'ativo')
            ->whereHas('sessionSchedules', function ($query) {
                $query->where('ativo', true);
            })
            ->with(['sessionSchedules' => function ($query) {
                $query->where('ativo', true);
            }])
            ->get();

        foreach ($sessions as $session) {
            foreach ($session->sessionSchedules as $schedule) {
                $appointmentsCreated += $this->createAppointmentsForSchedule(
                    $session,
                    $schedule,
                    $startDate,
                    $endDate
                );
            }
        }

        return $appointmentsCreated;
    }

    /**
     * Create appointments for a specific fixed schedule within a period
     */
    private function createAppointmentsForSchedule(
        TherapySession $therapySession,
        SessionSchedule $schedule,
        Carbon $startDate,
        Carbon $endDate
    ): int {
        $appointmentsCreated = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Check if day of week matches the schedule
            // Carbon's dayOfWeek: 0=Sunday, 1=Monday, etc.
            // Our dia_da_semana: 1=Monday, 2=Tuesday, etc.
            $carbonDayOfWeek = $currentDate->dayOfWeek === 0 ? 7 : $currentDate->dayOfWeek;
            
            if ($carbonDayOfWeek === $schedule->dia_da_semana) {
                // Create appointment date/time
                $appointmentDateTime = $currentDate->copy()->setTimeFromTimeString($schedule->hora);
                
                // Check if appointment already exists
                if (!$this->appointmentExists($therapySession, $schedule, $appointmentDateTime)) {
                    // Check if session is not complete
                    if (!$therapySession->isCompleta()) {
                        // Check if professional is available
                        if ($this->isProfessionalAvailable(
                            $therapySession->professional_id,
                            $appointmentDateTime,
                            $appointmentDateTime->copy()->addMinutes($schedule->duracao_minutos)
                        )) {
                            Appointment::create([
                                'therapy_session_id' => $therapySession->id,
                                'session_schedule_id' => $schedule->id,
                                'patient_id' => $therapySession->patient_id,
                                'address_id' => $schedule->address_id,
                                'professional_id' => $therapySession->professional_id,
                                'data_hora_inicio' => $appointmentDateTime,
                                'data_hora_fim' => $appointmentDateTime->copy()->addMinutes($schedule->duracao_minutos),
                                'status' => 'agendado',
                                'observacoes' => 'Agendamento automático',
                            ]);

                            $appointmentsCreated++;
                        }
                    }
                }
            }

            $currentDate->addDay();
        }

        return $appointmentsCreated;
    }

    /**
     * Check if an appointment already exists
     */
    private function appointmentExists(
        TherapySession $therapySession,
        SessionSchedule $schedule,
        Carbon $dateTime
    ): bool {
        return Appointment::where('therapy_session_id', $therapySession->id)
            ->where('session_schedule_id', $schedule->id)
            ->where('data_hora_inicio', $dateTime)
            ->exists();
    }

    /**
     * Check if professional is available at a specific time
     */
    private function isProfessionalAvailable(
        int $professionalId,
        Carbon $startDateTime,
        Carbon $endDateTime
    ): bool {
        return !Appointment::where('professional_id', $professionalId)
            ->where('status', '!=', 'cancelado')
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('data_hora_inicio', [$startDateTime, $endDateTime])
                    ->orWhereBetween('data_hora_fim', [$startDateTime, $endDateTime])
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where('data_hora_inicio', '<=', $startDateTime)
                          ->where('data_hora_fim', '>=', $endDateTime);
                    });
            })
            ->exists();
    }

    /**
     * Suggest available time slots for an appointment
     */
    public function suggestAvailableSlots(
        TherapySession $therapySession,
        Carbon $date,
        int $durationMinutes = 60
    ): array {
        $availableSlots = [];
        $dayStart = $date->copy()->setTime(8, 0); // Business hours start
        $dayEnd = $date->copy()->setTime(18, 0);  // Business hours end

        $currentTime = $dayStart->copy();

        while ($currentTime < $dayEnd) {
            $slotEnd = $currentTime->copy()->addMinutes($durationMinutes);

            if ($this->isProfessionalAvailable($therapySession->professional_id, $currentTime, $slotEnd)) {
                $availableSlots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'start_datetime' => $currentTime->toDateTimeString(),
                    'end_datetime' => $slotEnd->toDateTimeString(),
                ];
            }

            $currentTime->addMinutes(30); // 30-minute intervals
        }

        return $availableSlots;
    }

    /**
     * Create a single appointment
     */
    public function createAppointment(array $data): Appointment
    {
        // Validate professional availability
        if (!$this->isProfessionalAvailable(
            $data['professional_id'],
            Carbon::parse($data['data_hora_inicio']),
            Carbon::parse($data['data_hora_fim'])
        )) {
            throw new \Exception('Professional is not available at this time.');
        }

        return Appointment::create($data);
    }

    /**
     * Reschedule an appointment
     */
    public function rescheduleAppointment(
        Appointment $appointment,
        Carbon $newStartDateTime,
        Carbon $newEndDateTime
    ): Appointment {
        if (!$this->isProfessionalAvailable(
            $appointment->professional_id,
            $newStartDateTime,
            $newEndDateTime
        )) {
            throw new \Exception('Professional is not available at this time.');
        }

        $appointment->update([
            'data_hora_inicio' => $newStartDateTime,
            'data_hora_fim' => $newEndDateTime,
        ]);

        return $appointment;
    }

    /**
     * Get upcoming appointments for a professional
     */
    public function getUpcomingAppointments(int $professionalId, int $days = 7)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($days);

        return Appointment::with(['patient', 'therapySession', 'address'])
            ->where('professional_id', $professionalId)
            ->whereBetween('data_hora_inicio', [$startDate, $endDate])
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->get();
    }

    /**
     * Get appointments for a specific day
     */
    public function getAppointmentsByDate(int $professionalId, Carbon $date)
    {
        return Appointment::with(['patient', 'therapySession', 'address'])
            ->where('professional_id', $professionalId)
            ->whereDate('data_hora_inicio', $date)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->get();
    }
}