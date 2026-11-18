<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\Pagamento;
use App\Models\SolicitacaoPagamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PortalClienteController extends Controller
{
    public function show(string $token)
    {
        $cliente = Cliente::where('portal_token', $token)
            ->where('portal_ativo', true)
            ->with(['contratos.parcelas.pagamentos'])
            ->firstOrFail();

        return view('portal-cliente.show', compact('cliente'));
    }

    public function recibo(string $token, int $pagamentoId)
    {
        $cliente = Cliente::where('portal_token', $token)
            ->where('portal_ativo', true)
            ->firstOrFail();

        $pagamento = Pagamento::with([
            'parcela.contrato.cliente',
            'parcela.pagamentos',
        ])
        ->whereKey($pagamentoId)
        ->whereHas('parcela.contrato', function ($query) use ($cliente) {
            $query->where('cliente_id', $cliente->id);
        })
        ->firstOrFail();

        $parcela = $pagamento->parcela;
        $contrato = $parcela->contrato;

        abort_unless($contrato->cliente_id === $cliente->id, 404);

        $totalRecebido = (float) $parcela->pagamentos->sum('valor');
        $saldoRestante = max(
            0,
            (float) $parcela->valor - $totalRecebido
        );

        $pdf = Pdf::loadView('pagamentos.recibo', compact(
            'pagamento',
            'parcela',
            'contrato',
            'cliente',
            'totalRecebido',
            'saldoRestante'
        ));

        return $pdf->download("recibo_pagamento_{$pagamento->id}.pdf");
    }

    public function store(Request $request, string $token)
    {
        $cliente = Cliente::where('portal_token', $token)
            ->where('portal_ativo', true)
            ->firstOrFail();

        $dados = $request->validate([
            'parcela_id' => ['required', 'integer'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_pagamento' => ['required', 'date'],
            'comprovante' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        $parcela = Parcela::whereKey($dados['parcela_id'])
            ->whereHas('contrato', function ($query) use ($cliente) {
                $query->where('cliente_id', $cliente->id);
            })
            ->with('contrato.parcelas.pagamentos')
            ->firstOrFail();

        $contrato = $parcela->contrato;

        $saldoContrato = 0;

        foreach ($contrato->parcelas as $item) {
            $totalPago = (float) $item->pagamentos->sum('valor');

            $saldoContrato += max(
                0,
                (float) $item->valor - $totalPago
            );
        }

        $valorSolicitado = round((float) $dados['valor'], 2);
        $saldoContrato = round($saldoContrato, 2);

        if ($valorSolicitado > $saldoContrato) {
            throw ValidationException::withMessages([
                'valor' => 'O valor informado ultrapassa o saldo total do contrato. Saldo disponível: R$ '
                    . number_format($saldoContrato, 2, ',', '.') . '.',
            ]);
        }

        if ($saldoContrato <= 0) {
            throw ValidationException::withMessages([
                'valor' => 'Este contrato já está totalmente quitado.',
            ]);
        }

        $caminho = $request->file('comprovante')
            ->store('solicitacoes-pagamento', 'local');

        if (!$caminho) {
            return back()->with(
                'error',
                'Não foi possível salvar o comprovante.'
            );
        }

        SolicitacaoPagamento::create([
            'parcela_id' => $parcela->id,
            'valor' => $valorSolicitado,
            'data_pagamento' => $dados['data_pagamento'],
            'comprovante' => $caminho,
            'observacao' => $dados['observacao'] ?? null,
            'status' => 'pendente',
        ]);

        return back()->with(
            'success',
            'Comprovante enviado! Aguarde a confirmação do pagamento.'
        );
    }
}
