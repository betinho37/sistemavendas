<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
    'cliente_id',
    'data_inicio',
    'valor',
    'juros',
    'qtd_parcelas',
    'intervalo_dias',
    'total_receber',
    'lucro',
    'valor_parcela',
    'observacao',
    'vencimento_sabado',
    'vencimento_domingo',
];


    protected $casts = [
        'valor'          => 'float',
        'juros'          => 'float',
        'total_receber'  => 'float',
        'lucro'          => 'float',
        'valor_parcela'  => 'float',
        'data_inicio'    => 'date',
    ];


    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }


    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class, 'contrato_id', 'id'); 
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contrato) {
            // Gera um número de contrato único
            $contrato->numero = self::gerarNumeroContrato();
        });
    }

    private static function gerarNumeroContrato()
    {
        // Lógica para gerar o número do contrato
        // Por exemplo, um número sequencial baseado no último registro
        $ultimoContrato = self::latest('id')->first();
        $ultimoNumero = $ultimoContrato ? intval($ultimoContrato->numero) : 0;

        return str_pad($ultimoNumero + 1, 6, '0', STR_PAD_LEFT);
    }

}
