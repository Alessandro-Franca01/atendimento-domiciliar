<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $table = 'appointments';
    
    protected $fillable = [
        'therapy_session_id',
        'session_schedule_id',
        'patient_id',
        'address_id',
        'professional_id',
        'data_hora_inicio',
        'data_hora_fim',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
    ];

    public function therapySession(): BelongsTo
    {
        return $this->belongsTo(TherapySession::class);
    }

    public function sessionSchedule(): BelongsTo
    {
        return $this->belongsTo(SessionSchedule::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    public function isFixo(): bool
    {
        return !is_null($this->session_schedule_id);
    }

    public function podeSerCancelado(): bool
    {
        return in_array($this->status, ['agendado', 'confirmado']);
    }

    public function podeSerConcluido(): bool
    {
        return $this->status === 'confirmado';
    }

    public function getDuracaoMinutosAttribute(): int
    {
        if (!$this->data_hora_inicio || !$this->data_hora_fim) {
            return 0;
        }
        return $this->data_hora_inicio->diffInMinutes($this->data_hora_fim);
    }
}