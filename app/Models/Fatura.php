<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fatura extends Model
{
    protected $fillable = [
        'paciente_id',
        'valor_total',
        'data_emissao',
        'data_vencimento',
        'status',
        'tipo',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(FaturaItem::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}