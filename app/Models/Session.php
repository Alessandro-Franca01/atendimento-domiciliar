<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    protected $table = 'therapy_sessions';
    
    protected $fillable = [
        'patient_id',
        'professional_id',
        'descricao',
        'total_sessoes',
        'valor_por_sessao',
        'desconto_percentual',
        'desconto_valor',
        'sessoes_realizadas',
        'data_inicio',
        'data_fim_prevista',
        'status',
        'valor_total',
        'valor_pago'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim_prevista' => 'date',
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'valor_por_sessao' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function sessionSchedules(): HasMany
    {
        return $this->hasMany(SessionSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isCompleta(): bool
    {
        return $this->sessoes_realizadas >= $this->total_sessoes;
    }

    public function getSessoesRestantesAttribute(): int
    {
        return max(0, $this->total_sessoes - $this->sessoes_realizadas);
    }

    public function getSaldoPagamentoAttribute(): float
    {
        $total = (float) ($this->valor_total ?? 0);
        $pago = (float) ($this->valor_pago ?? 0);
        return max(0.0, $total - $pago);
    }
}