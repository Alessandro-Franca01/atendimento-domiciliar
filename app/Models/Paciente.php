<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $fillable = [
        'nome',
        'telefone',
        'documento',
        'observacoes',
        'status'
    ];

    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class);
    }

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

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }
}
