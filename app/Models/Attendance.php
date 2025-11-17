<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendances';
    
    protected $fillable = [
        'appointment_id',
        'professional_id',
        'patient_id',
        'data_realizacao',
        'valor',
        'evolucao',
        'procedimento_realizado',
        'assinatura_paciente',
        'status',
        'status_pagamento'
    ];

    protected $casts = [
        'data_realizacao' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected static function booted()
    {
        static::created(function ($attendance) {
            // Incrementar sessões realizadas da sessão
            $session = $attendance->appointment->session;
            $session->increment('sessoes_realizadas');
            
            // Verificar se a sessão foi concluída
            if ($session->sessoes_realizadas >= $session->total_sessoes) {
                $session->update(['status' => 'concluido']);
            }

            if (($session->valor_total ?? null) !== null && (float) ($session->saldo_pagamento ?? 0) === 0.0) {
                $attendance->update(['status_pagamento' => 'pago_via_sessao']);
            }
        });
    }
}