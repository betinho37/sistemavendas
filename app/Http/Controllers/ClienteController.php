<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    private function clientesPermitidos()
    {
        $user = Auth::user();

        return Cliente::query()
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }

    public function index(Request $request)
    {
        $busca = trim($request->input('busca', ''));
        $ordem = $request->input('ordem', 'recentes');

        if (!in_array($ordem, ['recentes', 'nome_asc', 'nome_desc'], true)) {
            $ordem = 'recentes';
        }

        $clientes = $this->clientesPermitidos()
            ->with('documentos')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('nome', 'like', "%{$busca}%")
                        ->orWhere('fone', 'like', "%{$busca}%")
                        ->orWhere('cpf', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->when($ordem === 'nome_asc', function ($query) {
                $query->orderByRaw('LOWER(nome) ASC')
                    ->orderBy('id', 'asc');
            })
            ->when($ordem === 'nome_desc', function ($query) {
                $query->orderByRaw('LOWER(nome) DESC')
                    ->orderBy('id', 'desc');
            })
            ->when($ordem === 'recentes', function ($query) {
                $query->latest();
            })
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact(
            'clientes',
            'busca',
            'ordem'
        ));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request)
    {
        $cliente = Cliente::create(array_merge(
            $request->except(['foto', 'documentos', 'user_id']),
            ['user_id' => Auth::id()]
        ));

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store(
                'clientes/fotos',
                'public'
            );

            $cliente->update(['foto' => $path]);
        }

        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $path = $file->store(
                    "clientes/{$cliente->id}/documentos",
                    'public'
                );

                $cliente->documentos()->create([
                    'nome' => $file->getClientOriginalName(),
                    'arquivo' => $path,
                ]);
            }
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente = $this->clientesPermitidos()
            ->with('documentos')
            ->findOrFail($cliente->id);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente = $this->clientesPermitidos()
            ->with('documentos')
            ->findOrFail($cliente->id);

        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $cliente = $this->clientesPermitidos()
            ->findOrFail($cliente->id);

        $dados = $request->except([
            'foto',
            'documentos',
            'user_id'
        ]);

        $cliente->update($dados);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store(
                'clientes/fotos',
                'public'
            );

            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
            }

            $cliente->update(['foto' => $path]);
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente = $this->clientesPermitidos()
            ->with('documentos')
            ->findOrFail($cliente->id);

        if ($cliente->foto) {
            Storage::disk('public')->delete($cliente->foto);
        }

        foreach ($cliente->documentos as $doc) {
            if ($doc->arquivo) {
                Storage::disk('public')->delete($doc->arquivo);
            }

            $doc->delete();
        }

        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente removido!');
    }
}
