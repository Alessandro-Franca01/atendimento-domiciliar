<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    
    protected $fillable = [
        'invoice_id',
        'descricao',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'attendance_id',
        'therapy_session_id',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function therapySession(): BelongsTo
    {
        return $this->belongsTo(TherapySession::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            // Calcular valor total automaticamente
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }
}