<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atendimento extends Model
{
    protected $fillable = [
        'agendamento_id',
        'profissional_id',
        'paciente_id',
        'data_realizacao',
        'evolucao',
        'procedimento_realizado',
        'assinatura_paciente',
        'status',
        'status_pagamento'
    ];

    protected $casts = [
        'data_realizacao' => 'datetime',
    ];

    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    protected static function booted()
    {
        static::created(function ($atendimento) {
            // Incrementar sessões realizadas da sessão
            $sessao = $atendimento->agendamento->sessao;
            $sessao->increment('sessoes_realizadas');
            
            // Verificar se a sessão foi concluída
            if ($sessao->sessoes_realizadas >= $sessao->total_sessoes) {
                $sessao->update(['status' => 'concluido']);
            }

            if (($sessao->valor_total ?? null) !== null && (float) ($sessao->saldo_pagamento ?? 0) === 0.0) {
                $atendimento->update(['status_pagamento' => 'pago_via_sessao']);
            }
        });
    }
}
