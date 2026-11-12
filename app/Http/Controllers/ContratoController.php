<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Parcela;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with('cliente')->get();
        return view('contratos.index', compact('contratos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('contratos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'     => 'required|exists:clientes,id',
            'valor'          => 'required|numeric|min:0',
            'juros'          => 'required|numeric|min:0',
            'qtd_parcelas'   => 'required|integer|min:1',
            'intervalo_dias' => 'required|integer|min:1',
            'data_inicio'    => 'required|date',
        ]);

        $valor    = $request->valor;
        $juros    = $request->juros / 100;
        $parcelas = $request->qtd_parcelas;

        // 🔥 PRICE
        $valorParcela = $valor * (
            ($juros * pow(1 + $juros, $parcelas)) /
            (pow(1 + $juros, $parcelas) - 1)
        );

        $totalReceber = $valorParcela * $parcelas;
        $lucro        = $totalReceber - $valor;

        $contrato = Contrato::create([
            'cliente_id'     => $request->cliente_id,
            'data_inicio'    => $request->data_inicio,
            'valor'          => $valor,
            'juros'          => $request->juros,
            'qtd_parcelas'   => $parcelas,
            'intervalo_dias' => $request->intervalo_dias,
            'total_receber'  => round($totalReceber, 2),
            'lucro'          => round($lucro, 2),
            'valor_parcela'  => round($valorParcela, 2),
            'observacao'     => $request->observacao,
        ]);

        $this->gerarParcelas($contrato);

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato criado com sucesso!');
    }

   public function show($id)
{
    $contrato = Contrato::with([
        'cliente',
        'parcelas.pagamentos.user'
    ])->findOrFail($id);

    return view('contratos.show', compact('contrato'));
}

    public function edit(Contrato $contrato)
    {
        $clientes = Cliente::all();
        return view('contratos.edit', compact('contrato', 'clientes'));
    }

   public function update(Request $request, Contrato $contrato)
{
    $request->validate([
        'cliente_id'     => 'required|exists:clientes,id',
        'valor'          => 'required|numeric|min:0',
        'juros'          => 'required|numeric|min:0',
        'qtd_parcelas'   => 'required|integer|min:1',
        'intervalo_dias' => 'required|integer|min:1',
        'data_inicio'    => 'required|date',
    ]);

    $valor    = $request->valor;
    $juros    = $request->juros / 100;
    $parcelas = $request->qtd_parcelas;

    // 🔥 PRICE
    $fator = pow(1 + $juros, $parcelas);
    $valorParcela = $valor * (($juros * $fator) / ($fator - 1));
    $totalReceber = $valorParcela * $parcelas;
    $lucro        = $totalReceber - $valor;

    $contrato->update([
        'cliente_id'        => $request->cliente_id,
        'valor'             => $valor,
        'juros'             => $request->juros,
        'qtd_parcelas'      => $parcelas,
        'intervalo_dias'    => $request->intervalo_dias,
        'data_inicio'       => $request->data_inicio,
        'total_receber'     => round($totalReceber, 2),
        'lucro'             => round($lucro, 2),
        'valor_parcela'     => round($valorParcela, 2),
        'observacao'        => $request->observacao,
        'vencimento_sabado' => $request->has('vencimento_sabado'),
        'vencimento_domingo'=> $request->has('vencimento_domingo'),
    ]);

    // Regerar parcelas
    $contrato->parcelas()->delete();
    $this->gerarParcelas($contrato);

    return redirect()
        ->route('contratos.show', $contrato->id)
        ->with('success', 'Contrato atualizado com sucesso!');
}

    private function gerarParcelas(Contrato $contrato)
    {
        $parcelas   = $contrato->qtd_parcelas;
        $intervalo  = $contrato->intervalo_dias;
        $dataInicio = Carbon::parse($contrato->data_inicio);

        for ($i = 1; $i <= $parcelas; $i++) {
            Parcela::create([
                'contrato_id'     => $contrato->id,
                'numero'          => $i,
                'data_vencimento' => $dataInicio->copy()->addDays(($i - 1) * $intervalo),
                'valor'           => round($contrato->valor_parcela, 2),
                'status'          => false,
            ]);
        }
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index');
    }

    public function gerarPdf($id)
    {
        $contrato = Contrato::with('cliente', 'parcelas')->findOrFail($id);
        $pdf = Pdf::loadView('contratos.pdf', compact('contrato'));
        return $pdf->stream("contrato_{$contrato->id}.pdf");
    }
}
