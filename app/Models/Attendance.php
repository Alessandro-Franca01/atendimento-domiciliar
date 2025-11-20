<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    protected static function booted()
    {
        static::created(function ($attendance) {
            // Incrementar sessões realizadas da sessão
            $therapySession = $attendance->appointment->therapySession;
            $therapySession->increment('sessoes_realizadas');
            
            // Verificar se a sessão foi concluída
            if ($therapySession->sessoes_realizadas >= $therapySession->total_sessoes) {
                $therapySession->update(['status' => 'concluido']);
            }

            // Atualizar status de pagamento se sessão já está paga
            if (($therapySession->valor_total ?? null) !== null && (float) ($therapySession->saldo_pagamento ?? 0) === 0.0) {
                $attendance->update(['status_pagamento' => 'pago_via_sessao']);
            }
        });
    }

    public function isPago(): bool
    {
        return in_array($this->status_pagamento, ['pago', 'pago_via_sessao']);
    }

    public function isPendente(): bool
    {
        return $this->status_pagamento === 'pendente';
    }
}