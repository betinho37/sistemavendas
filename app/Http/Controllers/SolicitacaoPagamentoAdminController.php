<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Parcela;
use App\Models\Pagamento;
use App\Models\SolicitacaoPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SolicitacaoPagamentoAdminController extends Controller
{
    private function verificarAdmin(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->isAdmin(),
            403
        );
    }

    public function index(Request $request)
    {
        $this->verificarAdmin();

        $status = $request->query('status', 'pendente');

        $query = SolicitacaoPagamento::with([
            'parcela.contrato.cliente',
        ])
            ->orderByRaw("CASE WHEN status = 'pendente' THEN 0 ELSE 1 END")
            ->latest();

        if (in_array($status, [
            'pendente',
            'aprovado',
            'rejeitado',
        ], true)) {
            $query->where('status', $status);
        } else {
            $status = 'todos';
        }

        $solicitacoes = $query->paginate(15)->withQueryString();

        $pendentes = SolicitacaoPagamento::where(
            'status',
            'pendente'
        )->count();

        return view('solicitacoes-pagamento.index', compact(
            'solicitacoes',
            'pendentes',
            'status'
        ));
    }

    public function show($id)
    {
        $this->verificarAdmin();

        $solicitacao = SolicitacaoPagamento::with([
            'parcela.contrato.cliente',
        ])->findOrFail($id);

        $contrato = $solicitacao->parcela->contrato;

        $parcelas = $contrato->parcelas()
            ->with('pagamentos')
            ->get();

        $totalPago = 0;
        $saldoRestante = 0;

        foreach ($parcelas as $parcela) {
            $pago = (float) $parcela->pagamentos->sum('valor');

            $totalPago += $pago;

            $saldoRestante += max(
                0,
                (float) $parcela->valor - $pago
            );
        }

        $totalPago = round($totalPago, 2);
        $saldoRestante = round($saldoRestante, 2);

        return view('solicitacoes-pagamento.show', compact(
            'solicitacao',
            'totalPago',
            'saldoRestante'
        ));
    }

    /**
     * Exibe o comprovante privado.
     */
    public function comprovante($id)
    {
        $this->verificarAdmin();

        $solicitacao = SolicitacaoPagamento::findOrFail($id);

        $disk = Storage::disk('local');
        $arquivo = $solicitacao->comprovante;

        abort_unless(
            $arquivo && $disk->exists($arquivo),
            404
        );

        $caminho = $disk->path($arquivo);
        $mime = $disk->mimeType($arquivo)
            ?: 'application/octet-stream';

        abort_unless(
            in_array($mime, [
                'application/pdf',
                'image/jpeg',
                'image/png',
            ], true),
            415
        );

        return response()->file($caminho, [
            'Content-Type' => $mime,
            'Content-Disposition' =>
                'inline; filename="comprovante_' . $solicitacao->id . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Aprova e distribui o pagamento entre as parcelas em aberto.
     */
    public function aprovar($id)
    {
        $this->verificarAdmin();

        DB::transaction(function () use ($id) {

            $solicitacao = SolicitacaoPagamento::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($solicitacao->status !== 'pendente') {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'Esta solicitação já foi processada.',
                ]);
            }

            $parcelaOrigem = Parcela::findOrFail(
                $solicitacao->parcela_id
            );

            // O bloqueio do contrato serializa aprovações
            // simultâneas referentes ao mesmo contrato.
            $contrato = Contrato::whereKey(
                $parcelaOrigem->contrato_id
            )
                ->lockForUpdate()
                ->firstOrFail();

            $parcelas = Parcela::where(
                'contrato_id',
                $contrato->id
            )
                ->orderBy('data_vencimento')
                ->orderBy('numero')
                ->lockForUpdate()
                ->get();

            $valorSolicitadoCentavos = (int) round(
                (float) $solicitacao->valor * 100
            );

            if ($valorSolicitadoCentavos <= 0) {
                throw ValidationException::withMessages([
                    'solicitacao' => 'O valor solicitado é inválido.',
                ]);
            }

            // Calcula os saldos atualizados em centavos.
            $saldos = [];
            $saldoTotalCentavos = 0;

            foreach ($parcelas as $parcela) {
                $valorParcelaCentavos = (int) round(
                    (float) $parcela->valor * 100
                );

                $totalPagoCentavos = (int) round(
                    (float) $parcela->pagamentos()->sum('valor') * 100
                );

                $saldoParcelaCentavos = max(
                    0,
                    $valorParcelaCentavos - $totalPagoCentavos
                );

                $saldos[$parcela->id] = [
                    'saldo' => $saldoParcelaCentavos,
                    'total_pago' => $totalPagoCentavos,
                    'valor' => $valorParcelaCentavos,
                ];

                $saldoTotalCentavos += $saldoParcelaCentavos;
            }

            if ($saldoTotalCentavos <= 0) {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'Este contrato já está totalmente quitado.',
                ]);
            }

            if ($valorSolicitadoCentavos > $saldoTotalCentavos) {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'O valor solicitado ultrapassa o saldo total do contrato. Saldo disponível: R$ '
                        . number_format(
                            $saldoTotalCentavos / 100,
                            2,
                            ',',
                            '.'
                        ) . '.',
                ]);
            }

            // Confere o comprovante antes de criar os pagamentos.
            $disk = Storage::disk('local');
            $arquivoOriginal = $solicitacao->comprovante;

            if (
                !$arquivoOriginal
                || !$disk->exists($arquivoOriginal)
            ) {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'O comprovante não foi encontrado. Não é possível aprovar este pagamento.',
                ]);
            }

            // Mantém uma cópia privada do comprovante.
            $disk->makeDirectory('comprovantes');

            $extensao = pathinfo(
                $arquivoOriginal,
                PATHINFO_EXTENSION
            );

            $novoComprovante = 'comprovantes/portal_'
                . $solicitacao->id
                . '_'
                . Str::uuid()
                . '.'
                . $extensao;

            if (!$disk->copy(
                $arquivoOriginal,
                $novoComprovante
            )) {
                throw new \RuntimeException(
                    'Não foi possível copiar o comprovante.'
                );
            }

            $valorRestanteCentavos = $valorSolicitadoCentavos;

            // Distribui o pagamento por ordem de vencimento.
            foreach ($parcelas as $parcela) {
                if ($valorRestanteCentavos <= 0) {
                    break;
                }

                $saldoParcelaCentavos =
                    $saldos[$parcela->id]['saldo'];

                if ($saldoParcelaCentavos <= 0) {
                    continue;
                }

                $valorAlocadoCentavos = min(
                    $valorRestanteCentavos,
                    $saldoParcelaCentavos
                );

                $valorAlocado = $valorAlocadoCentavos / 100;

                $observacao = trim(
                    (string) $solicitacao->observacao
                );

                $complemento = 'Pagamento via Portal - Solicitação #'
                    . $solicitacao->id
                    . ' - Parcela '
                    . $parcela->numero
                    . '.';

                $observacao = $observacao !== ''
                    ? $observacao . "\n" . $complemento
                    : $complemento;

                Pagamento::create([
                    'parcela_id' => $parcela->id,
                    'user_id' => Auth::id(),
                    'valor' => $valorAlocado,
                    'data_pagamento' => $solicitacao->data_pagamento,
                    'observacao' => $observacao,
                    'comprovante' => $novoComprovante,
                ]);

                $novoTotalPagoCentavos =
                    $saldos[$parcela->id]['total_pago']
                    + $valorAlocadoCentavos;

                $parcela->update([
                    'status' => $novoTotalPagoCentavos
                        >= $saldos[$parcela->id]['valor'],
                ]);

                $valorRestanteCentavos -=
                    $valorAlocadoCentavos;
            }

            if ($valorRestanteCentavos > 0) {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'Não foi possível distribuir todo o valor entre as parcelas.',
                ]);
            }

            $solicitacao->update([
                'status' => 'aprovado',
                'aprovado_por' => Auth::id(),
                'aprovado_em' => now(),
            ]);
        });

        return redirect()
            ->route('admin.solicitacoes-pagamento.index')
            ->with(
                'success',
                'Pagamento aprovado e distribuído entre as parcelas em aberto!'
            );
    }

    /**
     * Rejeita a solicitação sem criar pagamentos.
     */
    public function rejeitar(Request $request, $id)
    {
        $this->verificarAdmin();

        $dados = $request->validate([
            'motivo_rejeicao' => [
                'required',
                'string',
                'min:3',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($id, $dados) {

            $solicitacao = SolicitacaoPagamento::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($solicitacao->status !== 'pendente') {
                throw ValidationException::withMessages([
                    'solicitacao' =>
                        'Esta solicitação já foi processada.',
                ]);
            }

            $solicitacao->update([
                'status' => 'rejeitado',
                'motivo_rejeicao' => $dados['motivo_rejeicao'],
                'aprovado_por' => Auth::id(),
                'aprovado_em' => now(),
            ]);
        });

        return redirect()
            ->route('admin.solicitacoes-pagamento.index')
            ->with(
                'success',
                'Solicitação rejeitada. Nenhum pagamento foi registrado.'
            );
    }
}
