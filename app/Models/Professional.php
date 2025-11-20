<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Professional extends Authenticatable
{
    use Notifiable;
    protected $table = 'professionals';
    
    protected $fillable = [
        'nome',
        'email',
        'password',
        'crefito',
        'cpf',
        'telefone',
        'data_nascimento',
        'foto',
        'sobre',
        'especialidades',
        'horario_funcionamento',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_nascimento' => 'date',
        'password' => 'hashed',
    ];

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
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