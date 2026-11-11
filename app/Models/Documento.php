<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{

    protected $table = 'cliente_documentos';
    
    protected $fillable = [
        'cliente_id',
        'nome',
        'arquivo',
        'extensao'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
