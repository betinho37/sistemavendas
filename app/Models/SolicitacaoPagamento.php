<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoPagamento extends Model
{
    protected $table = 'solicitacoes_pagamento';

    protected $fillable = [
        'parcela_id',
        'valor',
        'data_pagamento',
        'comprovante',
        'status',
        'observacao',
        'motivo_rejeicao',
        'aprovado_por',
        'aprovado_em',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'aprovado_em' => 'datetime',
    ];

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class);
    }
}
