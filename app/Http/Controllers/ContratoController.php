<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0',
            'juros' => 'required|numeric|min:0',
            'qtd_parcelas' => 'required|integer|min:1',
            'intervalo_dias' => 'required|integer|min:1',
            'data_inicio' => 'required|date',
        ]);

        $valor = $request->valor;
        $juros = $request->juros / 100;
        $parcelas = $request->qtd_parcelas;

        if ($juros > 0) {
            $fator = pow(1 + $juros, $parcelas);
            $valorParcela = $valor * (($juros * $fator) / ($fator - 1));
        } else {
            $valorParcela = $valor / $parcelas;
        }

        $totalReceber = $valorParcela * $parcelas;
        $lucro = $totalReceber - $valor;

        $contrato = Contrato::create([
            'cliente_id' => $request->cliente_id,
            'data_inicio' => $request->data_inicio,
            'valor' => $valor,
            'juros' => $request->juros,
            'qtd_parcelas' => $parcelas,
            'intervalo_dias' => $request->intervalo_dias,
            'total_receber' => round($totalReceber, 2),
            'lucro' => round($lucro, 2),
            'valor_parcela' => round($valorParcela, 2),
            'observacao' => $request->observacao,
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
        if ($contrato->parcelas()->whereHas('pagamentos')->exists()
            || $contrato->parcelas()->where('status', true)->exists()) {
            return back()->withErrors([
                'contrato' => 'Este contrato já possui parcelas pagas ou recebimentos registrados. Edite as parcelas individualmente para preservar o histórico.'
            ])->withInput();
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0',
            'juros' => 'required|numeric|min:0',
            'qtd_parcelas' => 'required|integer|min:1',
            'intervalo_dias' => 'required|integer|min:1',
            'data_inicio' => 'required|date',
        ]);

        $valor = $request->valor;
        $juros = $request->juros / 100;
        $parcelas = $request->qtd_parcelas;

        if ($juros > 0) {
            $fator = pow(1 + $juros, $parcelas);
            $valorParcela = $valor * (($juros * $fator) / ($fator - 1));
        } else {
            $valorParcela = $valor / $parcelas;
        }

        $totalReceber = $valorParcela * $parcelas;
        $lucro = $totalReceber - $valor;

        DB::transaction(function () use ($request, $contrato, $valor, $parcelas, $valorParcela, $totalReceber, $lucro) {

            $contrato->update([
                'cliente_id' => $request->cliente_id,
                'valor' => $valor,
                'juros' => $request->juros,
                'qtd_parcelas' => $parcelas,
                'intervalo_dias' => $request->intervalo_dias,
                'data_inicio' => $request->data_inicio,
                'total_receber' => round($totalReceber, 2),
                'lucro' => round($lucro, 2),
                'valor_parcela' => round($valorParcela, 2),
                'observacao' => $request->observacao,
                'vencimento_sabado' => $request->has('vencimento_sabado'),
                'vencimento_domingo' => $request->has('vencimento_domingo'),
            ]);

            $contrato->parcelas()->delete();

            $this->gerarParcelas($contrato);
        });

        return redirect()
            ->route('contratos.show', $contrato->id)
            ->with('success', 'Contrato atualizado com sucesso!');
    }

    public function updateParcela(Request $request, $id)
    {
        $dados = $request->validate([
            'numero' => ['required', 'integer', 'min:1'],
            'data_vencimento' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
        ]);

        return DB::transaction(function () use ($id, $dados) {

            $parcela = Parcela::with('contrato.cliente', 'pagamentos')
                ->lockForUpdate()
                ->findOrFail($id);

            $user = Auth::user();

            if (!$user->isAdmin()) {
                abort_unless(
                    $parcela->contrato->cliente->user_id == $user->id,
                    403
                );
            }

            $totalPago = round((float) $parcela->pagamentos()->sum('valor'), 2);
            $novoValor = round((float) $dados['valor'], 2);

            if ($totalPago > $novoValor) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor da parcela não pode ser menor que o total já recebido (R$ '
                        . number_format($totalPago, 2, ',', '.') . ').',
                ]);
            }

            $statusAtual = (bool) $parcela->status;

            $parcela->update([
                'numero' => $dados['numero'],
                'data_vencimento' => $dados['data_vencimento'],
                'valor' => $novoValor,
                'status' => $totalPago > 0
                    ? $totalPago >= $novoValor
                    : $statusAtual,
            ]);

            return back()->with(
                'success',
                'Parcela atualizada com sucesso!'
            );
        });
    }


private function gerarParcelas(Contrato $contrato)
{
    $quantidade = (int) $contrato->qtd_parcelas;
    $intervalo = (int) $contrato->intervalo_dias;

    $taxa = (float) $contrato->juros / 100;

    $saldoDevedor = round((float) $contrato->valor, 2);

    $valorParcela = round((float) $contrato->valor_parcela, 2);

    $dataInicio = Carbon::parse($contrato->data_inicio);

    for ($i = 1; $i <= $quantidade; $i++) {

        $juros = round($saldoDevedor * $taxa, 2);

        $valorAtual = $valorParcela;

        // Ajusta a última parcela para encerrar o saldo devedor.
        if ($i === $quantidade) {
            $valorAtual = round($saldoDevedor + $juros, 2);
        }

        $amortizacao = round($valorAtual - $juros, 2);

        $amortizacao = min($amortizacao, $saldoDevedor);

        $saldoDevedor = max(
            0,
            round($saldoDevedor - $amortizacao, 2)
        );

        Parcela::create([
            'contrato_id' => $contrato->id,
            'numero' => $i,
            'data_vencimento' => $dataInicio->copy()->addDays(
                ($i - 1) * $intervalo
            ),
            'valor' => $valorAtual,
            'juros_valor' => $juros,
            'amortizacao' => $amortizacao,
            'saldo_devedor' => $saldoDevedor,
            'status' => false,
        ]);
    }

    // Atualiza o total real das parcelas geradas.
    $totalReceber = round(
        (float) $contrato->parcelas()->sum('valor'),
        2
    );

    $contrato->update([
        'total_receber' => $totalReceber,
        'lucro' => round($totalReceber - $contrato->valor, 2),
    ]);
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
