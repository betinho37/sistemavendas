<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\MovimentacaoCaixa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FluxoCaixaController extends Controller
{
    private function contratosPermitidos()
    {
        $user = Auth::user();

        $query = Contrato::query();

        if (!$user->isAdmin()) {
            $query->whereHas('cliente', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    private function movimentacoesPermitidas()
    {
        $user = Auth::user();

        $query = MovimentacaoCaixa::query();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $dados = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ]);

        $inicio = Carbon::parse(
            $dados['data_inicio'] ?? now()->startOfMonth()->toDateString()
        )->startOfDay();

        $fim = Carbon::parse(
            $dados['data_fim'] ?? now()->endOfMonth()->toDateString()
        )->endOfDay();

        $contratos = $this->contratosPermitidos()
            ->with(['cliente', 'parcelas.pagamentos'])
            ->get();

        $movimentacoes = $this->movimentacoesPermitidas()
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->get();

        $entradasManuais = 0;
        $saidasManuais = 0;
        $entradasManuaisPeriodo = 0;
        $saidasManuaisPeriodo = 0;

        foreach ($movimentacoes as $movimentacao) {
            $valor = (float) $movimentacao->valor;

            if ($movimentacao->tipo === 'entrada') {
                $entradasManuais += $valor;
            } else {
                $saidasManuais += $valor;
            }

            if ($movimentacao->data->betweenIncluded($inicio, $fim)) {
                if ($movimentacao->tipo === 'entrada') {
                    $entradasManuaisPeriodo += $valor;
                } else {
                    $saidasManuaisPeriodo += $valor;
                }
            }
        }

        $totalEmprestado = 0;
        $totalEmprestadoPeriodo = 0;
        $totalRecebido = 0;
        $totalRecebidoPeriodo = 0;
        $capitalRecuperado = 0;
        $capitalRecuperadoPeriodo = 0;
        $lucroRecebido = 0;
        $lucroRecebidoPeriodo = 0;
        $lucroPrevisto = 0;

        $historico = [];

        foreach ($contratos as $contrato) {
            $valorContrato = (float) $contrato->valor;
            $dataContrato = $contrato->data_inicio;

            $totalEmprestado += $valorContrato;

            if ($dataContrato && $dataContrato->betweenIncluded($inicio, $fim)) {
                $totalEmprestadoPeriodo += $valorContrato;

                $historico[] = [
                    'data' => $dataContrato->format('Y-m-d'),
                    'tipo' => 'saida',
                    'categoria' => 'Empréstimo concedido',
                    'descricao' => 'Contrato #' . $contrato->id
                        . ' - ' . ($contrato->cliente->nome ?? 'Cliente'),
                    'valor' => $valorContrato,
                ];
            }

            $saldoDevedor = $valorContrato;

            foreach ($contrato->parcelas->sortBy('numero') as $parcela) {
                $valorParcela = round((float) $parcela->valor, 2);

                if ($parcela->juros_valor !== null) {
                    $jurosParcela = (float) $parcela->juros_valor;
                } else {
                    $jurosParcela = round(
                        $saldoDevedor * ((float) $contrato->juros / 100),
                        2
                    );
                }

                $jurosParcela = min(
                    max(0, $jurosParcela),
                    $valorParcela
                );

                $amortizacao = max(
                    0,
                    round($valorParcela - $jurosParcela, 2)
                );

                $saldoDevedor = max(
                    0,
                    round($saldoDevedor - $amortizacao, 2)
                );

                $proporcaoJuros = $valorParcela > 0
                    ? $jurosParcela / $valorParcela
                    : 0;

                $jurosPagosParcela = 0;

                foreach ($parcela->pagamentos as $pagamento) {
                    $valorPago = (float) $pagamento->valor;

                    // Divide cada recebimento proporcionalmente entre juros e capital.
                    $jurosPagamento = round(
                        $valorPago * $proporcaoJuros,
                        2
                    );

                    $jurosPagamento = min($jurosPagamento, $valorPago);

                    $capitalPagamento = round(
                        $valorPago - $jurosPagamento,
                        2
                    );

                    $jurosPagosParcela += $jurosPagamento;

                    $totalRecebido += $valorPago;
                    $lucroRecebido += $jurosPagamento;
                    $capitalRecuperado += $capitalPagamento;

                    if ($pagamento->data_pagamento->betweenIncluded($inicio, $fim)) {
                        $totalRecebidoPeriodo += $valorPago;
                        $lucroRecebidoPeriodo += $jurosPagamento;
                        $capitalRecuperadoPeriodo += $capitalPagamento;

                        $historico[] = [
                            'data' => $pagamento->data_pagamento->format('Y-m-d'),
                            'tipo' => 'entrada',
                            'categoria' => 'Recebimento de parcela',
                            'descricao' => 'Parcela ' . $parcela->numero
                                . ' - Contrato #' . $contrato->id
                                . ' - ' . ($contrato->cliente->nome ?? 'Cliente'),
                            'valor' => $valorPago,
                        ];
                    }
                }

                $lucroPrevisto += max(
                    0,
                    round($jurosParcela - $jurosPagosParcela, 2)
                );
            }
        }

        // Movimentações manuais dentro do período selecionado.
        foreach ($movimentacoes as $movimentacao) {
            if (!$movimentacao->data->betweenIncluded($inicio, $fim)) {
                continue;
            }

            $historico[] = [
                'data' => $movimentacao->data->format('Y-m-d'),
                'tipo' => $movimentacao->tipo,
                'categoria' => $movimentacao->categoria,
                'descricao' => $movimentacao->descricao,
                'valor' => (float) $movimentacao->valor,
                'id' => $movimentacao->id,
                'manual' => true,
            ];
        }

        // Saldo considera todo o histórico, não apenas o período filtrado.
        $saldoCaixa = round(
            $entradasManuais
            - $saidasManuais
            + $totalRecebido
            - $totalEmprestado,
            2
        );

        $entradasPeriodo = round(
            $entradasManuaisPeriodo + $totalRecebidoPeriodo,
            2
        );

        $saidasPeriodo = round(
            $saidasManuaisPeriodo + $totalEmprestadoPeriodo,
            2
        );

        usort($historico, function ($a, $b) {
            return strcmp($b['data'], $a['data']);
        });

        return view('fluxo-caixa.index', compact(
            'inicio',
            'fim',
            'movimentacoes',
            'historico',
            'saldoCaixa',
            'entradasPeriodo',
            'saidasPeriodo',
            'totalEmprestado',
            'totalRecebido',
            'capitalRecuperado',
            'lucroRecebido',
            'lucroPrevisto'
        ));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'tipo' => ['required', Rule::in(['entrada', 'saida'])],
            'categoria' => ['required', 'string', 'max:100'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'data' => ['required', 'date'],
            'descricao' => ['nullable', 'string', 'max:2000'],
        ]);

        $dados['user_id'] = Auth::id();

        MovimentacaoCaixa::create($dados);

        return redirect()
            ->route('fluxo-caixa.index')
            ->with('success', 'Movimentação registrada com sucesso!');
    }

    public function destroy($id)
    {
        $movimentacao = $this->movimentacoesPermitidas()
            ->findOrFail($id);

        $movimentacao->delete();

        return redirect()
            ->route('fluxo-caixa.index')
            ->with('success', 'Movimentação excluída com sucesso!');
    }
}
