<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =====================
        // FILTRO CLIENTE
        // =====================
        $clienteId = $request->cliente_id;

        // =====================
        // CONTADORES
        // =====================
        $clientes  = Cliente::count();
        $contratos = Contrato::count();

        $parcelasPagas = Parcela::where('status', true)->count();
        $parcelasVencidas = Parcela::where('data_vencimento', '<', now())
            ->where('status', false)
            ->count();

        $parcelasAVencer = Parcela::where('data_vencimento', '>=', now())
            ->where('status', false)
            ->count();

        $capitalInvestido = Contrato::sum('valor');

        $lucroObtido = Parcela::where('parcelas.status', true)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->selectRaw('
                COALESCE(
                    SUM((contratos.valor * contratos.juros / 100) / contratos.qtd_parcelas),
                    0
                ) as lucro
            ')->value('lucro');

        $previsaoLucro = Parcela::where('parcelas.status', false)
            ->join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->selectRaw('
                COALESCE(
                    SUM((contratos.valor * contratos.juros / 100) / contratos.qtd_parcelas),
                    0
                ) as lucro
            ')->value('lucro');

        // =====================
        // PARCELAS COM JOIN COMPLETO
        // =====================
       $parcelasCalendario = Parcela::join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
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
        ->groupBy(fn ($p) => $p->data_vencimento->format('Y-m-d'));



        $listaClientes = Cliente::orderBy('nome')->get();

        $rankingClientes = Parcela::join('contratos', 'parcelas.contrato_id', '=', 'contratos.id')
            ->join('clientes', 'contratos.cliente_id', '=', 'clientes.id')
            ->where('parcelas.status', true) // só parcelas pagas
            ->select(
                'clientes.nome',
                DB::raw('SUM((contratos.valor * contratos.juros / 100) / contratos.qtd_parcelas) as lucro')
            )
            ->groupBy('clientes.nome')
            ->orderByDesc('lucro')
            ->limit(5)
            ->get();

            
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

    // =====================
    // MARCAR PARCELA COMO PAGA
    // =====================
    public function pagarParcela(Parcela $parcela)
    {
        $parcela->update(['status' => true]);

        return response()->json([
            'success' => true
        ]);
    }

    // =====================
    // EXPORTAR PDF
    // =====================
    public function exportarPdf(Request $request)
    {
        $parcelas = Parcela::with('contrato.cliente')
            ->where('status', false)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboard.pdf',
            compact('parcelas')
        );

        return $pdf->download('parcelas-dashboard.pdf');
    }
}
