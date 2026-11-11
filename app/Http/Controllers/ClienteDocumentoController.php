<?php


namespace App\Http\Controllers;

use App\Models\ClienteDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteDocumentoController extends Controller
{
    /**
     * Envia documentos para um cliente.
     */
    public function store(Request $request, $clienteId)
    {
        $request->validate([
            'documentos'   => 'required|array',
            'documentos.*' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $retorno = [];

        foreach ($request->file('documentos') as $file) {
            $path = $file->store(
                "clientes/{$clienteId}/documentos",
                'public'
            );

            $doc = ClienteDocumento::create([
                'cliente_id' => $clienteId,
                'nome'       => $file->getClientOriginalName(),
                'arquivo'    => $path,
            ]);

            $retorno[] = [
                'id'         => $doc->id,
                'nome'       => $doc->nome,
                'arquivo'    => asset('storage/' . $path),
                'created_at' => $doc->created_at
                    ? $doc->created_at->format('d/m/Y H:i')
                    : null,
            ];
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Documentos enviados com sucesso.',
            'documentos' => $retorno,
        ]);
    }

    /**
     * Exclui um documento.
     */
    public function destroy(ClienteDocumento $documento)
    {
        $arquivo = ltrim($documento->arquivo, '/');

        if (str_starts_with($arquivo, 'storage/')) {
            $arquivo = substr($arquivo, strlen('storage/'));
        }

        if ($arquivo !== '' && Storage::disk('public')->exists($arquivo)) {
            Storage::disk('public')->delete($arquivo);
        }

        $documento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento excluído com sucesso.',
        ]);
    }

    /**
     * Renomeia um documento sem alterar o arquivo físico.
     */
    public function rename(Request $request, $clienteId, $id)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $nome = trim($dados['nome']);

        if ($nome === '') {
            return response()->json([
                'success' => false,
                'message' => 'Informe um nome válido para o documento.',
            ], 422);
        }

        // Localiza o documento pertencente ao cliente informado.
        $documento = ClienteDocumento::where('cliente_id', $clienteId)
            ->findOrFail($id);

        // Atribuição direta evita depender de $fillable no model.
        $documento->nome = $nome;
        $documento->save();

        // Confirma que a alteração foi persistida.
        $documento->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Nome do documento atualizado com sucesso.',
            'documento' => [
                'id'   => $documento->id,
                'nome' => $documento->nome,
            ],
        ]);
    }
}
