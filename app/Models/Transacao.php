<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacoes'; // Especifica o nome da tabela
    protected $fillable = ['cliente_id', 'tipo', 'valor', 'data', 'descricao'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
