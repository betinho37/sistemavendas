<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'data_nascimento',
        'genero',
        'estado_civil',
        'endereco',
        'fone',
        'cpf',
        'email',
        'foto',
        'observacoes'
    ];

    public function documentos()
    {
        return $this->hasMany(ClienteDocumento::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
}
