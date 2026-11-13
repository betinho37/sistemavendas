<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Pagamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        $pagamento = DB::transaction(function () use ($parcela, $dados, $arquivo) {
            $parcela = Parcela::whereKey($parcela->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->verificarAcesso($parcela);

            $totalPago = (float) $parcela->pagamentos()->sum('valor');
            $valorParcela = (float) $parcela->valor;
            $saldo = round($valorParcela - $totalPago, 2);
            $valorPagamento = round((float) $dados['valor'], 2);

            if ($parcela->status && $totalPago == 0) {
                throw ValidationException::withMessages([
                    'valor' => 'Esta parcela está marcada como paga, mas não possui histórico de recebimentos.',
                ]);
            }

            if ($saldo <= 0) {
                throw ValidationException::withMessages([
                    'valor' => 'Esta parcela já está totalmente paga.',
                ]);
            }

            if ($valorPagamento > $saldo) {
                throw ValidationException::withMessages([
                    'valor' => 'O pagamento ultrapassa o saldo restante de R$ '
                        . number_format($saldo, 2, ',', '.') . '.',
                ]);
            }

            $pagamento = $parcela->pagamentos()->create([
                'user_id' => Auth::id(),
                'valor' => $valorPagamento,
                'data_pagamento' => $dados['data_pagamento'],
            ]);

            if ($arquivo) {
                $pagamento->comprovante = $arquivo->store('comprovantes', 'local');
                $pagamento->save();
            }

            $this->atualizarStatus($parcela);

            return $pagamento;
        });

        return redirect()->route('pagamentos.recibo', $pagamento->id);
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

            $totalAtual = (float) $parcela->pagamentos()->sum('valor');
            $outrosPagamentos = $totalAtual - (float) $pagamento->valor;
            $novoValor = round((float) $dados['valor'], 2);

            if (round($outrosPagamentos + $novoValor, 2) > (float) $parcela->valor) {
                throw ValidationException::withMessages([
                    'valor' => 'A soma dos recebimentos ultrapassa o valor da parcela.',
                ]);
            }

            $pagamento->valor = $novoValor;
            $pagamento->data_pagamento = $dados['data_pagamento'];

            if ($arquivo) {
                $novoComprovante = $arquivo->store('comprovantes', 'local');

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

        abort_unless(
            $pagamento->comprovante &&
            Storage::disk('local')->exists($pagamento->comprovante),
            404
        );

        return response()->file(
            Storage::disk('local')->path($pagamento->comprovante)
        );
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
