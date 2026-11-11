<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome'            => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'genero'          => 'nullable|string',
            'estado_civil'    => 'nullable|string',
            'endereco'        => 'required|string',
            'fone'            => 'required|string',
            'cpf'             => 'required|string',
            'email'           => 'required|email',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'documentos.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'observacoes'     => 'nullable|string',
        ];
    }
}
