<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    protected $fillable = [
        'parcela_id',
        'user_id',
        'valor',
        'data_pagamento',
        'observacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
    ];

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function pagamentos()
{
    return $this->hasMany(Pagamento::class);
}

public function getTotalPagoAttribute(): float
{
    return (float) $this->pagamentos()->sum('valor');
}

public function getSaldoRestanteAttribute(): float
{
    return max(0, (float) $this->valor - $this->total_pago);
}
}
