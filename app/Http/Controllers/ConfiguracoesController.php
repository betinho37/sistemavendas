<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ConfiguracoesController extends Controller
{
    // Método para exibir a view de configurações
    public function index()
    {
        // Lê arquivo de configurações salvo anteriormente
        $config = [];
        if (Storage::disk('local')->exists('configuracoes.json')) {
            $config = json_decode(Storage::get('configuracoes.json'), true);
        }

        return view('config.config', compact('config'));
    }

    // Método para processar o formulário de salvamento
    public function salvar(Request $request)
    { $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'telefone' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'logo' => 'nullable|image|max:2048',
        'juros_padrao' => 'nullable|numeric|min:0',
        'intervalo' => 'nullable|integer|min:1',
        'pix' => 'nullable|string|max:255',
        'notificacao_email' => 'nullable|boolean',
        'notificacao_whatsapp' => 'nullable|boolean',
        'dias_aviso' => 'nullable|integer|min:0',
    ]);

    $dados = $validated;

    // Checkbox fallback (caso não enviados)
    $dados['notificacao_email'] = $request->has('notificacao_email');
    $dados['notificacao_whatsapp'] = $request->has('notificacao_whatsapp');

    if ($request->hasFile('logo')) {
        $dados['logo_path'] = $request->file('logo')->store('logos', 'public');
    }

    Storage::disk('local')->put('configuracoes.json', json_encode($dados));

    return redirect()->route('configuracoes.index')->with('success', 'Configurações salvas com sucesso!');

    }
}