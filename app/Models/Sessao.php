<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sessao extends Model
{
    protected $fillable = [
        'paciente_id',
        'profissional_id',
        'descricao',
        'total_sessoes',
        'sessoes_realizadas',
        'data_inicio',
        'data_fim_prevista',
        'status'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim_prevista' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }

    public function sessaoHorarios(): HasMany
    {
        return $this->hasMany(SessaoHorario::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    public function isCompleta(): bool
    {
        return $this->sessoes_realizadas >= $this->total_sessoes;
    }

    public function getSessoesRestantesAttribute(): int
    {
        return max(0, $this->total_sessoes - $this->sessoes_realizadas);
    }
}
