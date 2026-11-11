<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDocumento extends Model
{
    protected $fillable = [
        'cliente_id',
        'nome',
        'arquivo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
