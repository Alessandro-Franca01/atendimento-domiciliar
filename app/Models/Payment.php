<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';
    
    protected $fillable = [
        'attendance_id',
        'therapy_session_id',
        'patient_id',
        'professional_id',
        'invoice_id',
        'metodo_pagamento',
        'valor',
        'data_pagamento',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'valor' => 'decimal:2',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function therapySession(): BelongsTo
    {
        return $this->belongsTo(TherapySession::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            // Atualizar status de pagamento do atendimento
            if ($payment->attendance_id) {
                $payment->attendance->update(['status_pagamento' => 'pago']);
            }

            // Atualizar valor pago da sessão
            if ($payment->therapy_session_id) {
                $therapySession = $payment->therapySession;
                $totalPago = $therapySession->payments()
                    ->where('status', 'pago')
                    ->sum('valor');
                $therapySession->update(['valor_pago' => $totalPago]);
            }
        });

        static::updated(function ($payment) {
            // Se pagamento foi estornado
            if ($payment->status === 'estornado') {
                if ($payment->attendance_id) {
                    $payment->attendance->update(['status_pagamento' => 'pendente']);
                }

                if ($payment->therapy_session_id) {
                    $therapySession = $payment->therapySession;
                    $totalPago = $therapySession->payments()
                        ->where('status', 'pago')
                        ->sum('valor');
                    $therapySession->update(['valor_pago' => $totalPago]);
                }
            }
        });
    }

    public function isPago(): bool
    {
        return $this->status === 'pago';
    }

    public function isEstornado(): bool
    {
        return $this->status === 'estornado';
    }
}