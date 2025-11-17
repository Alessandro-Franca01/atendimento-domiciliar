<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profissional extends Model
{
    protected $fillable = [
        'nome',
        'crefito',
        'telefone',
        'especialidades',
        'horario_funcionamento',
        'status'
    ];

    protected $casts = [
        'especialidades' => 'array',
    ];

    public function sessoes(): HasMany
    {
        return $this->hasMany(Sessao::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }
}
