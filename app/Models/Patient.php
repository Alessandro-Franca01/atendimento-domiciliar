<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $table = 'patients';
    
    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'cpf',
        'data_nascimento',
        'convenio',
        'numero_whatsapp',
        'observacoes',
        'status'
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}