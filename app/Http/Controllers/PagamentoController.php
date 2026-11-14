
<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Pagamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PagamentoController extends Controller
{
    private function verificarAcesso(Parcela $parcela): void
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            $permitida = $parcela->contrato()
                ->whereHas('cliente', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->exists();

            abort_unless($permitida, 403);
        }
    }

    private function verificarAcessoPagamento(Pagamento $pagamento): void
    {
        $pagamento->loadMissing('parcela.contrato.cliente');
        $this->verificarAcesso($pagamento->parcela);
    }

    private function atualizarStatus(Parcela $parcela): void
    {
        $totalPago = round((float) $parcela->pagamentos()->sum('valor'), 2);
        $valorParcela = round((float) $parcela->valor, 2);

        $parcela->update([
            'status' => $totalPago >= $valorParcela,
        ]);
    }

    public function store(Request $request, Parcela $parcela)
    {
        $dados = $request->validate([
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_pagamento' => ['required', 'date'],
            'comprovante' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $arquivo = $request->file('comprovante');
        unset($dados['comprovante']);

        DB::transaction(function () use ($parcela, $dados, $arquivo) {
            $parcelaAtual = Parcela::whereKey($parcela->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->verificarAcesso($parcelaAtual);

            $valorPagamento = round((float) $dados['valor'], 2);

            $pagamento = $parcelaAtual->pagamentos()->create([
                'user_id' => Auth::id(),
                'valor' => $valorPagamento,
                'data_pagamento' => $dados['data_pagamento'],
            ]);

            if ($arquivo) {
                $caminho = $arquivo->store('comprovantes', 'local');

                if (!$caminho) {
                    throw new \RuntimeException('Não foi possível salvar o comprovante.');
                }

                $pagamento->comprovante = $caminho;
                $pagamento->save();
            }

            $this->atualizarStatus($parcelaAtual);
        });

        return back()->with(
            'success',
            'Pagamento registrado e comprovante anexado com sucesso!'
        );
    }

    public function update(Request $request, $pagamentoId)
    {
        $dados = $request->validate([
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_pagamento' => ['required', 'date'],
            'comprovante' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $arquivo = $request->file('comprovante');
        unset($dados['comprovante']);

        return DB::transaction(function () use ($pagamentoId, $dados, $arquivo) {
            $parcela = Parcela::whereHas('pagamentos', function ($query) use ($pagamentoId) {
                $query->where('id', $pagamentoId);
            })->lockForUpdate()->firstOrFail();

            $this->verificarAcesso($parcela);

            $pagamento = $parcela->pagamentos()
                ->whereKey($pagamentoId)
                ->lockForUpdate()
                ->firstOrFail();

            $novoValor = round((float) $dados['valor'], 2);

            $pagamento->valor = $novoValor;
            $pagamento->data_pagamento = $dados['data_pagamento'];

            if ($arquivo) {
                $novoComprovante = $arquivo->store('comprovantes', 'local');

                if (!$novoComprovante) {
                    throw new \RuntimeException('Não foi possível salvar o comprovante.');
                }

                if ($pagamento->comprovante) {
                    Storage::disk('local')->delete($pagamento->comprovante);
                }

                $pagamento->comprovante = $novoComprovante;
            }

            $pagamento->save();

            $this->atualizarStatus($parcela);

            return back()->with('success', 'Recebimento atualizado com sucesso!');
        });
    }

    public function comprovante($pagamentoId)
    {
        $pagamento = Pagamento::with('parcela.contrato.cliente')
            ->findOrFail($pagamentoId);

        $this->verificarAcessoPagamento($pagamento);

        $disk = Storage::disk('local');
        $arquivo = $pagamento->comprovante;

        abort_unless($arquivo && $disk->exists($arquivo), 404);

        $caminho = $disk->path($arquivo);
        $mime = $disk->mimeType($arquivo) ?: 'application/octet-stream';

        return response()->stream(function () use ($caminho) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $handle = fopen($caminho, 'rb');

            if ($handle !== false) {
                fpassthru($handle);
                fclose($handle);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($arquivo) . '"',
            'Content-Length' => (string) filesize($caminho),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function baixarComprovante($pagamentoId)
    {
        $pagamento = Pagamento::with('parcela.contrato.cliente')
            ->findOrFail($pagamentoId);

        $this->verificarAcessoPagamento($pagamento);

        abort_unless(
            $pagamento->comprovante &&
            Storage::disk('local')->exists($pagamento->comprovante),
            404
        );

        return Storage::disk('local')->download(
            $pagamento->comprovante,
            'comprovante_pagamento_' . $pagamento->id
        );
    }

    public function destroy($pagamentoId)
    {
        return DB::transaction(function () use ($pagamentoId) {
            $parcela = Parcela::whereHas('pagamentos', function ($query) use ($pagamentoId) {
                $query->where('id', $pagamentoId);
            })->lockForUpdate()->firstOrFail();

            $this->verificarAcesso($parcela);

            $pagamento = $parcela->pagamentos()
                ->whereKey($pagamentoId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pagamento->comprovante) {
                Storage::disk('local')->delete($pagamento->comprovante);
            }

            $pagamento->delete();

            $this->atualizarStatus($parcela);

            return back()->with('success', 'Recebimento excluído com sucesso!');
        });
    }

    public function recibo($pagamentoId)
    {
        $pagamento = Pagamento::with([
            'parcela.contrato.cliente',
            'parcela.pagamentos',
        ])->findOrFail($pagamentoId);

        $this->verificarAcessoPagamento($pagamento);

        $parcela = $pagamento->parcela;
        $contrato = $parcela->contrato;
        $cliente = $contrato->cliente;

        $totalRecebido = (float) $parcela->pagamentos->sum('valor');
        $saldoRestante = max(0, (float) $parcela->valor - $totalRecebido);

        $pdf = Pdf::loadView('pagamentos.recibo', compact(
            'pagamento',
            'parcela',
            'contrato',
            'cliente',
            'totalRecebido',
            'saldoRestante'
        ));

        return $pdf->stream("recibo_pagamento_{$pagamento->id}.pdf");
    }
}
