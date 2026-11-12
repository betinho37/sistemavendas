<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcela extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato_id',
        'numero',
        'data_vencimento',
        'valor',
        'juros_valor',
        'amortizacao',
        'saldo_devedor',
        'status',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'valor' => 'decimal:2',
        'juros_valor' => 'decimal:2',
        'amortizacao' => 'decimal:2',
        'saldo_devedor' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }

    public function cliente(): HasOneThrough
    {
        return $this->hasOneThrough(
            Cliente::class,
            Contrato::class,
            'id',
            'id',
            'contrato_id',
            'cliente_id'
        );
    }

    public function pagamentos(): HasMany
{
    return $this->hasMany(Pagamento::class, 'parcela_id');
}
}
