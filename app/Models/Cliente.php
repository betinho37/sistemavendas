<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'endereco',
        'fone',
        'cpf',
        'email',
        'data_nascimento',
        'genero',
        'estado_civil',
        'foto',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ClienteDocumento::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }
}
