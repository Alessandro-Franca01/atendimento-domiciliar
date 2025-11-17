<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endereco extends Model
{
    protected $fillable = [
        'paciente_id',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'cep',
        'complemento',
        'tipo'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function sessaoHorarios(): HasMany
    {
        return $this->hasMany(SessaoHorario::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
