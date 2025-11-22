<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $table = 'addresses';
    
    protected $fillable = [
        'patient_id',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'cep',
        'complemento',
        'tipo'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function sessionSchedules(): HasMany
    {
        return $this->hasMany(SessionSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getEnderecoCompletoAttribute(): string
    {
        return "{$this->logradouro}, {$this->numero} - {$this->bairro}, {$this->cidade} - CEP: {$this->cep}";
    }
}