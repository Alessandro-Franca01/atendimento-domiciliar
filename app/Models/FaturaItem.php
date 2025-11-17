<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaturaItem extends Model
{
    protected $fillable = [
        'fatura_id',
        'descricao',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'atendimento_id',
        'sessao_id',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }

    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class);
    }

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }
}