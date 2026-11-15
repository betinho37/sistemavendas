<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientePortalController extends Controller
{
    private function clientePermitido(Cliente $cliente): Cliente
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $cliente->user_id !== $user->id) {
            abort(403);
        }

        return $cliente;
    }

    public function gerar(Cliente $cliente)
    {
        $cliente = $this->clientePermitido($cliente);

        $cliente->forceFill([
            'portal_token' => Str::random(64),
            'portal_ativo' => true,
        ])->save();

        return back()->with('success', 'Link do portal gerado com sucesso!');
    }

    public function revogar(Cliente $cliente)
    {
        $cliente = $this->clientePermitido($cliente);

        $cliente->forceFill([
            'portal_ativo' => false,
            'portal_token' => null,
        ])->save();

        return back()->with('success', 'Acesso ao portal revogado!');
    }
}
