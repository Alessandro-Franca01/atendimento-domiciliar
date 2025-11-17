<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{
    protected $table = 'professionals';
    
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