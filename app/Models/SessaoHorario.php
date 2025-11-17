<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessaoHorario extends Model
{
    protected $fillable = [
        'sessao_id',
        'dia_da_semana',
        'hora',
        'duracao_minutos',
        'endereco_id',
        'ativo'
    ];

    protected $casts = [
        'hora' => 'datetime:H:i',
        'ativo' => 'boolean',
    ];

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    public function getDiaSemanaNomeAttribute(): string
    {
        $dias = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return $dias[$this->dia_da_semana] ?? 'Desconhecido';
    }
}
