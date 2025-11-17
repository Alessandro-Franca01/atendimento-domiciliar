<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agendamento extends Model
{
    protected $fillable = [
        'sessao_id',
        'sessao_horario_id',
        'paciente_id',
        'endereco_id',
        'profissional_id',
        'data_hora_inicio',
        'data_hora_fim',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
    ];

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function sessaoHorario(): BelongsTo
    {
        return $this->belongsTo(SessaoHorario::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }

    public function atendimento(): HasOne
    {
        return $this->hasOne(Atendimento::class);
    }

    public function isFixo(): bool
    {
        return !is_null($this->sessao_horario_id);
    }

    public function podeSerCancelado(): bool
    {
        return in_array($this->status, ['agendado', 'confirmado']);
    }

    public function podeSerConcluido(): bool
    {
        return $this->status === 'confirmado';
    }
}
