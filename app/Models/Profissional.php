<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Professional extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'professionals';

    protected $fillable = [
        'nome',
        'email',
        'password',
        'crefito',
        'cpf',
        'data_nascimento',
        'foto',
        'sobre',
        'telefone',
        'especialidades',
        'horario_funcionamento',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'especialidades' => 'array',
        'data_nascimento' => 'date',
        'password' => 'hashed',
    ];

    public function therapySessions(): HasMany
    {
        return $this->hasMany(TherapySession::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}