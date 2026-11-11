<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::latest()->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request)
    {
        $cliente = Cliente::create(
            $request->except(['foto', 'documentos'])
        );

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
                    'clientes/documentos',
                    'public'
                );

                $cliente->documentos()->create([
                    'nome'    => $file->getClientOriginalName(),
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
        $cliente->load('documentos');

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load('documentos');

        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        // Atualiza os dados do cliente sem sobrescrever a foto existente.
        $dados = $request->except(['foto', 'documentos']);

        $cliente->update($dados);

        // Atualiza a foto somente se uma nova for enviada.
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

        // Após salvar, retorna para a listagem de clientes.
        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        if (
            $cliente->foto &&
            Storage::disk('public')->exists($cliente->foto)
        ) {
            Storage::disk('public')->delete($cliente->foto);
        }

        foreach ($cliente->documentos as $doc) {
            if (
                $doc->arquivo &&
                Storage::disk('public')->exists($doc->arquivo)
            ) {
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
