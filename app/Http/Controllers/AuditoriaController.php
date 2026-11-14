<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $query = Auditoria::with('user')->latest();

        if ($request->filled('usuario')) {
            $query->where('user_id', $request->usuario);
        }

        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('inicio')) {
            $query->whereDate('created_at', '>=', $request->inicio);
        }

        if ($request->filled('fim')) {
            $query->whereDate('created_at', '<=', $request->fim);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;

            $query->where(function ($q) use ($busca) {
                $q->where('descricao', 'like', "%{$busca}%")
                    ->orWhere('usuario_nome', 'like', "%{$busca}%")
                    ->orWhere('registro_id', $busca);
            });
        }

        $auditorias = $query->paginate(20)->withQueryString();

        $usuarios = User::orderBy('name')->get();

        return view('auditoria.index', compact(
            'auditorias',
            'usuarios'
        ));
    }
}
