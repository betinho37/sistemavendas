<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuarioAdminController extends Controller
{
    private function verificarMaster(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }

    public function index(Request $request)
    {
        $this->verificarMaster();

        $query = User::query()
            ->withCount('clientes')
            ->select(['id', 'name', 'email', 'is_admin', 'created_at']);

        if ($request->filled('busca')) {
            $busca = trim($request->busca);

            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        if ($request->tipo === 'admin') {
            $query->where('is_admin', true);
        } elseif ($request->tipo === 'usuario') {
            $query->where('is_admin', false);
        }

        $usuarios = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $totalUsuarios = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        $totalComuns = User::where('is_admin', false)->count();

        $editando = null;

        if ($request->filled('editar')) {
            $editando = User::findOrFail($request->integer('editar'));
        }

        return view('admin.usuarios.index', compact(
            'usuarios',
            'totalUsuarios',
            'totalAdmins',
            'totalComuns',
            'editando'
        ));
    }

    public function store(Request $request)
    {
        $this->verificarMaster();

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['required', 'boolean'],
        ]);

        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => $dados['password'],
        ]);

        $usuario->forceFill([
            'is_admin' => (bool) $dados['is_admin'],
        ])->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function update(Request $request, User $usuario)
    {
        $this->verificarMaster();

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'is_admin' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $novoAdmin = (bool) $dados['is_admin'];
        $usuarioAtual = auth()->user();

        if ($usuario->id === $usuarioAtual->id && !$novoAdmin) {
            throw ValidationException::withMessages([
                'is_admin' => 'Você não pode retirar sua própria permissão de administrador.',
            ]);
        }

        if ($usuario->is_admin && !$novoAdmin) {
            $quantidadeAdmins = User::where('is_admin', true)->count();

            if ($quantidadeAdmins <= 1) {
                throw ValidationException::withMessages([
                    'is_admin' => 'Não é possível retirar o último administrador do sistema.',
                ]);
            }
        }

        $usuario->name = $dados['name'];
        $usuario->email = $dados['email'];
        $usuario->forceFill(['is_admin' => $novoAdmin]);

        if (!empty($dados['password'])) {
            $usuario->password = $dados['password'];
        }

        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario)
    {
        $this->verificarMaster();

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        if ($usuario->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'Não é possível excluir o último administrador.');
        }

        if ($usuario->clientes()->exists()) {
            return back()->with(
                'error',
                'Este usuário possui clientes vinculados. Transfira os clientes antes de excluir a conta.'
            );
        }

        try {
            DB::transaction(function () use ($usuario) {
                $usuario->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Não foi possível excluir esta conta porque existem registros vinculados a ela.'
            );
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
