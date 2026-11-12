<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private function verificarLink(string $token): array
    {
        abort_unless(
            Storage::disk('local')->exists('configuracoes.json'),
            404
        );

        $config = json_decode(
            Storage::disk('local')->get('configuracoes.json'),
            true
        );

        abort_unless(
            is_array($config)
            && ($config['cadastro_liberado'] ?? false)
            && !empty($config['cadastro_token'])
            && hash_equals(
                (string) $config['cadastro_token'],
                $token
            ),
            404,
            'Link de cadastro inválido ou desativado.'
        );

        return $config;
    }

    public function create(string $token): View
    {
        $this->verificarLink($token);

        return view('auth.register', compact('token'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $this->verificarLink($token);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        event(new Registered($user));

        // O novo usuário não recebe privilégios administrativos.
        // Não realiza login automático.

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Conta criada com sucesso. Faça login para acessar.'
            );
    }
}
