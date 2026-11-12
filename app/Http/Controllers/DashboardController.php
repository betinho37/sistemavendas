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
    /**
     * Consulta base de contratos permitidos.
     * Administrador visualiza todos.
     * Usuário comum visualiza somente seus contratos.
     */
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

    /**
     * Consulta base de parcelas permitidas.
     */
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

    /**
     * Dashboard principal.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filtro de cliente
        $clienteId = $request->cliente_id;

        // Consulta de clientes permitidos
        $clientesQuery = Cliente::query();

        if (!$user->isAdmin()) {
            $clientesQuery->where('user_id', $user->id);
        }

        // Consulta de contratos e parcelas permitidos
        $contratosQuery = $this->contratosPermitidos();
        $parcelasQuery = $this->parcelasPermitidas();

        // Aplica filtro de cliente somente se ele for permitido
        if ($clienteId) {
            $clientePermitido = (clone $clientesQuery)
                ->where('id', $clienteId)
                ->exists();

            if (!$clientePermitido) {
                abort(403, 'Você não tem permissão para acessar os dados deste cliente.');
            }

            $contratosQuery->where('cliente_id', $clienteId);

            $parcelasQuery->whereHas('contrato', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            });
        }

        // Contadores
        $clientes = (clone $clientesQuery)->count();

        if ($clienteId) {
            $clientes = 1;
        }

        $contratos = (clone $contratosQuery)->count();

        $parcelasPagas = (clone $parcelasQuery)
            ->where('status', true)
            ->count();

        $parcelasVencidas = (clone $parcelasQuery)
            ->where('data_vencimento', '<', now()->toDateString())
            ->where('status', false)
            ->count();

        $parcelasAVencer = (clone $parcelasQuery)
            ->where('data_vencimento', '>=', now()->toDateString())
            ->where('status', false)
            ->count();

        // Capital investido
        $capitalInvestido = (clone $contratosQuery)->sum('valor');

        // Lucro obtido com parcelas pagas
        $lucroObtido = (clone $parcelasQuery)
            ->where('parcelas.status', true)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->selectRaw('
                COALESCE(
                    SUM(
                        (contratos.valor * contratos.juros / 100)
                        / NULLIF(contratos.qtd_parcelas, 0)
                    ),
                    0
                ) as lucro
            ')
            ->value('lucro');

        // Previsão de lucro das parcelas ainda não pagas
        $previsaoLucro = (clone $parcelasQuery)
            ->where('parcelas.status', false)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->selectRaw('
                COALESCE(
                    SUM(
                        (contratos.valor * contratos.juros / 100)
                        / NULLIF(contratos.qtd_parcelas, 0)
                    ),
                    0
                ) as lucro
            ')
            ->value('lucro');

        // Calendário de parcelas
        $parcelasCalendario = (clone $parcelasQuery)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->join('clientes', 'contratos.cliente_id', '=', 'clientes.id')
            ->select(
                'parcelas.id',
                'parcelas.data_vencimento',
                'parcelas.valor',
                'parcelas.status',
                'contratos.id as contrato_id',
                'clientes.nome as cliente'
            )
            ->orderBy('parcelas.data_vencimento')
            ->get()
            ->groupBy(function ($parcela) {
                return $parcela->data_vencimento->format('Y-m-d');
            });

        // Lista de clientes permitidos
        $listaClientes = (clone $clientesQuery)
            ->orderBy('nome')
            ->get();

        // Ranking de clientes por lucro
        $rankingQuery = (clone $parcelasQuery)
            ->where('parcelas.status', true)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->join('clientes', 'contratos.cliente_id', '=', 'clientes.id')
            ->select(
                'clientes.nome',
                DB::raw('
                    SUM(
                        (contratos.valor * contratos.juros / 100)
                        / NULLIF(contratos.qtd_parcelas, 0)
                    ) as lucro
                ')
            )
            ->groupBy('clientes.id', 'clientes.nome')
            ->orderByDesc('lucro')
            ->limit(5);

        if ($clienteId) {
            $rankingQuery->where('clientes.id', $clienteId);
        }

        $rankingClientes = $rankingQuery->get();

        return view('dashboard', compact(
            'clientes',
            'rankingClientes',
            'contratos',
            'parcelasVencidas',
            'parcelasAVencer',
            'parcelasPagas',
            'capitalInvestido',
            'lucroObtido',
            'previsaoLucro',
            'parcelasCalendario',
            'listaClientes',
            'clienteId'
        ));
    }

    /**
     * Marcar parcela como paga.
     */
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
            'status' => true
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Exportar PDF das parcelas pendentes.
     */
    public function exportarPdf(Request $request)
    {
        $user = Auth::user();

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
