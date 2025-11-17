<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionSchedule extends Model
{
    protected $table = 'session_schedules';
    
    protected $fillable = [
        'session_id',
        'dia_da_semana',
        'hora',
        'duracao_minutos',
        'address_id',
        'ativo'
    ];

    protected $casts = [
        'hora' => 'datetime:H:i',
        'ativo' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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