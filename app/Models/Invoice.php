<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $table = 'invoices';
    
    protected $fillable = [
        'patient_id',
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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isVencida(): bool
    {
        return $this->status === 'aberta' && $this->data_vencimento->isPast();
    }

    public function isPaga(): bool
    {
        return $this->status === 'paga';
    }

    public function getValorPagoAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', 'pago')
            ->sum('valor');
    }

    public function getSaldoAttribute(): float
    {
        return max(0, (float) $this->valor_total - $this->valor_pago);
    }
}