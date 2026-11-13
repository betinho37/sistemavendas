<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    private function parcelasPermitidas()
    {
        $user = Auth::user();

        $query = Parcela::query();

        if (!$user->isAdmin()) {
            $query->whereHas('contrato.cliente', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $clienteId = $request->cliente_id;

        $clientesQuery = Cliente::query();

        if (!$user->isAdmin()) {
            $clientesQuery->where('user_id', $user->id);
        }

        $contratosQuery = $this->contratosPermitidos();
        $parcelasQuery = $this->parcelasPermitidas();

        if ($clienteId) {
            $clientePermitido = (clone $clientesQuery)
                ->where('id', $clienteId)
                ->exists();

            abort_unless(
                $clientePermitido,
                403,
                'Você não tem permissão para acessar os dados deste cliente.'
            );

            $contratosQuery->where('cliente_id', $clienteId);

            $parcelasQuery->whereHas('contrato', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            });
        }

        $listaClientes = (clone $clientesQuery)
            ->orderBy('nome')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Contratos e parcelas com pagamentos
        |--------------------------------------------------------------------------
        */

        $contratosFinanceiros = (clone $contratosQuery)
            ->with([
                'cliente',
                'parcelas.pagamentos',
            ])
            ->get();

        $clientes = $clienteId
            ? 1
            : $listaClientes->count();

        $contratos = $contratosFinanceiros->count();

        $capitalInvestido = 0;
        $capitalRecuperado = 0;
        $totalRecebido = 0;
        $lucroObtido = 0;
        $previsaoLucro = 0;

        $parcelasPagas = 0;
        $parcelasVencidas = 0;
        $parcelasAVencer = 0;

        $rankingDados = [];

        $hoje = now()->toDateString();

        foreach ($contratosFinanceiros as $contrato) {

            $capitalContrato = (float) $contrato->valor;

            $capitalInvestido += $capitalContrato;

            $taxa = (float) $contrato->juros / 100;

            $saldoDevedor = $capitalContrato;

            $parcelasContrato = $contrato->parcelas
                ->sortBy('numero');

            foreach ($parcelasContrato as $parcela) {

                $valorParcela = round((float) $parcela->valor, 2);

                /*
                 * Usa os juros gravados na parcela.
                 * Para parcelas antigas sem composição financeira,
                 * calcula os juros pelo saldo devedor e taxa do contrato.
                 */

                if ($parcela->juros_valor !== null) {
                    $jurosParcela = (float) $parcela->juros_valor;
                } else {
                    $jurosParcela = round($saldoDevedor * $taxa, 2);
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

                /*
                 * Recebimentos reais da parcela.
                 */

                $pago = round(
                    (float) $parcela->pagamentos->sum('valor'),
                    2
                );

                $totalRecebido += $pago;

                /*
                 * Considera os juros recebidos primeiro.
                 * O restante do pagamento amortiza o capital.
                 */

                $jurosRecebidos = min(
                    $pago,
                    $jurosParcela
                );

                $capitalRecebidoParcela = min(
                    $amortizacao,
                    max(0, round($pago - $jurosParcela, 2))
                );

                $lucroObtido += $jurosRecebidos;

                $capitalRecuperado += $capitalRecebidoParcela;

                /*
                 * Lucro que ainda falta receber nesta parcela.
                 */

                $jurosRestantes = max(
                    0,
                    round($jurosParcela - $jurosRecebidos, 2)
                );

                $previsaoLucro += $jurosRestantes;

                /*
                 * Situação da parcela.
                 */

                $estaPaga = $parcela->status || $pago >= $valorParcela;

                if ($estaPaga) {
                    $parcelasPagas++;
                } elseif ($parcela->data_vencimento->toDateString() < $hoje) {
                    $parcelasVencidas++;
                } else {
                    $parcelasAVencer++;
                }

                /*
                 * Ranking por lucro efetivamente recebido.
                 */

                $clienteNome = $contrato->cliente->nome ?? 'Cliente';

                $clienteKey = $contrato->cliente_id;

                if (!isset($rankingDados[$clienteKey])) {
                    $rankingDados[$clienteKey] = [
                        'nome' => $clienteNome,
                        'lucro' => 0,
                    ];
                }

                $rankingDados[$clienteKey]['lucro'] += $jurosRecebidos;
            }
        }

        $capitalInvestido = round($capitalInvestido, 2);
        $capitalRecuperado = round($capitalRecuperado, 2);
        $totalRecebido = round($totalRecebido, 2);
        $lucroObtido = round($lucroObtido, 2);
        $previsaoLucro = round($previsaoLucro, 2);

        $capitalEmAberto = max(
            0,
            round($capitalInvestido - $capitalRecuperado, 2)
        );

        $lucroTotalPrevisto = round(
            $lucroObtido + $previsaoLucro,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Calendário financeiro
        |--------------------------------------------------------------------------
        */

        $parcelasCalendario = (clone $parcelasQuery)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->join('clientes', 'contratos.cliente_id', '=', 'clientes.id')
            ->select(
                'parcelas.id',
                'parcelas.numero as numero_parcela',
                'parcelas.data_vencimento',
                'parcelas.valor',
                'parcelas.status',
                'contratos.id as contrato_id',
                'clientes.nome as cliente'
            )
            ->selectSub(function ($query) {
                $query->from('pagamentos')
                    ->selectRaw('COALESCE(SUM(pagamentos.valor), 0)')
                    ->whereColumn('pagamentos.parcela_id', 'parcelas.id');
            }, 'total_pago')
            ->orderBy('parcelas.data_vencimento')
            ->get()
            ->groupBy(function ($parcela) {
                return \Carbon\Carbon::parse(
                    $parcela->data_vencimento
                )->format('Y-m-d');
            });

        /*
        |--------------------------------------------------------------------------
        | Ranking de clientes
        |--------------------------------------------------------------------------
        */

        $rankingClientes = collect($rankingDados)
            ->map(function ($dados) {
                return (object) [
                    'nome' => $dados['nome'],
                    'lucro' => round($dados['lucro'], 2),
                ];
            })
            ->sortByDesc('lucro')
            ->take(5)
            ->values();

        return view('dashboard', compact(
            'clientes',
            'rankingClientes',
            'contratos',
            'parcelasVencidas',
            'parcelasAVencer',
            'parcelasPagas',
            'capitalInvestido',
            'capitalRecuperado',
            'capitalEmAberto',
            'totalRecebido',
            'lucroObtido',
            'previsaoLucro',
            'lucroTotalPrevisto',
            'parcelasCalendario',
            'listaClientes',
            'clienteId'
        ));
    }

    public function pagarParcela(Parcela $parcela)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            $permitida = $this->parcelasPermitidas()
                ->where('parcelas.id', $parcela->id)
                ->exists();

            abort_unless(
                $permitida,
                403,
                'Você não tem permissão para alterar esta parcela.'
            );
        }

        $parcela->update([
            'status' => true,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function exportarPdf(Request $request)
    {
        $parcelas = $this->parcelasPermitidas()
            ->with('contrato.cliente')
            ->where('status', false)
            ->orderBy('data_vencimento')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboard.pdf',
            compact('parcelas')
        );

        return $pdf->download('parcelas-dashboard.pdf');
    }
}
