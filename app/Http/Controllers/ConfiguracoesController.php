<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfiguracoesController extends Controller
{
    private function verificarAdministrador(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->isAdmin(),
            403,
            'Somente o administrador pode acessar as configurações.'
        );
    }

    private function carregarConfiguracoes(): array
    {
        if (!Storage::disk('local')->exists('configuracoes.json')) {
            return [];
        }

        $dados = json_decode(
            Storage::disk('local')->get('configuracoes.json'),
            true
        );

        return is_array($dados) ? $dados : [];
    }

    public function index()
    {
        $this->verificarAdministrador();

        $config = $this->carregarConfiguracoes();

        return view('config.config', compact('config'));
    }

    public function salvar(Request $request)
    {
        $this->verificarAdministrador();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',

            'juros_padrao' => 'nullable|numeric|min:0|max:1000',
            'intervalo' => 'nullable|integer|min:1|max:365',
            'pix' => 'nullable|string|max:255',

            'dias_aviso' => 'nullable|integer|min:0|max:365',

            'notificacao_email' => 'nullable|boolean',
            'notificacao_whatsapp' => 'nullable|boolean',
            'cadastro_liberado' => 'nullable|boolean',
        ]);

        $config = $this->carregarConfiguracoes();

        // Não salva o objeto UploadedFile dentro do JSON.
        unset($validated['logo']);

        $dados = array_merge($config, $validated);

        $dados['notificacao_email'] =
            $request->boolean('notificacao_email');

        $dados['notificacao_whatsapp'] =
            $request->boolean('notificacao_whatsapp');

        $dados['cadastro_liberado'] =
            $request->boolean('cadastro_liberado');

        $dados['dias_aviso'] = $request->input('dias_aviso', 3);

        // Gerencia o link exclusivo de cadastro.
        if ($dados['cadastro_liberado']) {
            if (empty($config['cadastro_token'])) {
                $dados['cadastro_token'] = Str::random(48);
            }
        } else {
            $dados['cadastro_token'] = null;
        }

        // Preserva a logo atual se nenhuma nova for enviada.
        $logoAntiga = $config['logo_path'] ?? null;

        if ($request->hasFile('logo')) {
            $novoCaminho = $request->file('logo')->store('logos', 'public');

            if (!$novoCaminho) {
                return back()
                    ->withInput()
                    ->withErrors(['logo' => 'Não foi possível salvar a logo.']);
            }

            $dados['logo_path'] = $novoCaminho;
        } else {
            $dados['logo_path'] = $logoAntiga;
        }

        $salvo = Storage::disk('local')->put(
            'configuracoes.json',
            json_encode(
                $dados,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_THROW_ON_ERROR
            )
        );

        if (!$salvo) {
            return back()
                ->withInput()
                ->withErrors(['configuracoes' => 'Não foi possível salvar as configurações.']);
        }

        // Exclui a logo antiga somente depois de salvar a nova configuração.
        if (
            $request->hasFile('logo') &&
            $logoAntiga &&
            $logoAntiga !== $dados['logo_path']
        ) {
            Storage::disk('public')->delete($logoAntiga);
        }

        return redirect()
            ->route('configuracoes.index')
            ->with('success', 'Configurações salvas com sucesso!');
    }
}
