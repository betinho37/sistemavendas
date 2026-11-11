<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Parcela extends Model
{
   

    protected $fillable = [
        'contrato_id', 'numero', 'data_vencimento', 'valor', 'status'
    ];

    public function contrato(): BelongsTo
{
    return $this->belongsTo(Contrato::class, 'contrato_id', 'id', 'data_inicio'); 
}

 public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    protected $casts = [
    'data_vencimento' => 'date',
    ];

}
