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

    /**
     * Calcula o total usando juros simples sobre o capital original.
     * O período é convertido em meses considerando 30 dias por mês.
     */
    private function calcularValoresContrato(
        float $valor,
        float $juros,
        int $quantidade,
        int $intervaloDias
    ): array {
        $principalCentavos = (int) round($valor * 100);
        $meses = ($quantidade * $intervaloDias) / 30;

        $jurosTotalCentavos = (int) round(
            $principalCentavos * ($juros / 100) * $meses
        );

        $totalCentavos = $principalCentavos + $jurosTotalCentavos;

        return [
            'principal_centavos' => $principalCentavos,
            'juros_centavos' => $jurosTotalCentavos,
            'total_centavos' => $totalCentavos,
            'valor_parcela' => round(
                ($totalCentavos / $quantidade) / 100,
                2
            ),
            'total_receber' => $totalCentavos / 100,
            'lucro' => $jurosTotalCentavos / 100,
        ];
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0.01',
            'juros' => 'required|numeric|min:0',
            'qtd_parcelas' => 'required|integer|min:1',
            'intervalo_dias' => 'required|integer|min:1',
            'data_inicio' => 'required|date',
        ]);

        $valor = round((float) $dados['valor'], 2);
        $juros = (float) $dados['juros'];
        $quantidade = (int) $dados['qtd_parcelas'];
        $intervalo = (int) $dados['intervalo_dias'];

        $calculo = $this->calcularValoresContrato(
            $valor,
            $juros,
            $quantidade,
            $intervalo
        );

        DB::transaction(function () use (
            $request,
            $dados,
            $valor,
            $juros,
            $quantidade,
            $intervalo,
            $calculo
        ) {
            $contrato = Contrato::create([
                'cliente_id' => $dados['cliente_id'],
                'data_inicio' => $dados['data_inicio'],
                'valor' => $valor,
                'juros' => $juros,
                'qtd_parcelas' => $quantidade,
                'intervalo_dias' => $intervalo,
                'total_receber' => $calculo['total_receber'],
                'lucro' => $calculo['lucro'],
                'valor_parcela' => $calculo['valor_parcela'],
                'observacao' => $request->observacao,
            ]);

            $this->gerarParcelas($contrato);
        });

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
        if (
            $contrato->parcelas()->whereHas('pagamentos')->exists()
            || $contrato->parcelas()->where('status', true)->exists()
        ) {
            return back()->withErrors([
                'contrato' => 'Este contrato já possui parcelas pagas ou recebimentos registrados. Edite as parcelas individualmente para preservar o histórico.'
            ])->withInput();
        }

        $dados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0.01',
            'juros' => 'required|numeric|min:0',
            'qtd_parcelas' => 'required|integer|min:1',
            'intervalo_dias' => 'required|integer|min:1',
            'data_inicio' => 'required|date',
        ]);

        $valor = round((float) $dados['valor'], 2);
        $juros = (float) $dados['juros'];
        $quantidade = (int) $dados['qtd_parcelas'];
        $intervalo = (int) $dados['intervalo_dias'];

        $calculo = $this->calcularValoresContrato(
            $valor,
            $juros,
            $quantidade,
            $intervalo
        );

        DB::transaction(function () use (
            $request,
            $dados,
            $contrato,
            $valor,
            $juros,
            $quantidade,
            $intervalo,
            $calculo
        ) {
            $contrato->update([
                'cliente_id' => $dados['cliente_id'],
                'valor' => $valor,
                'juros' => $juros,
                'qtd_parcelas' => $quantidade,
                'intervalo_dias' => $intervalo,
                'data_inicio' => $dados['data_inicio'],
                'total_receber' => $calculo['total_receber'],
                'lucro' => $calculo['lucro'],
                'valor_parcela' => $calculo['valor_parcela'],
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

            $totalPago = round(
                (float) $parcela->pagamentos()->sum('valor'),
                2
            );

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

    /**
     * Gera parcelas com juros simples sobre o capital original.
     * Principal e juros são distribuídos em centavos, garantindo
     * que a soma das parcelas corresponda exatamente ao total.
     */
    private function gerarParcelas(Contrato $contrato)
    {
        $quantidade = (int) $contrato->qtd_parcelas;
        $intervalo = (int) $contrato->intervalo_dias;

        if ($quantidade < 1) {
            throw ValidationException::withMessages([
                'qtd_parcelas' => 'A quantidade de parcelas deve ser maior que zero.',
            ]);
        }

        $principalCentavos = (int) round(
            (float) $contrato->valor * 100
        );

        $meses = ($quantidade * $intervalo) / 30;

        $jurosTotalCentavos = (int) round(
            $principalCentavos *
            ((float) $contrato->juros / 100) *
            $meses
        );

        $principalBase = intdiv($principalCentavos, $quantidade);
        $principalResto = $principalCentavos % $quantidade;

        $jurosBase = intdiv($jurosTotalCentavos, $quantidade);
        $jurosResto = $jurosTotalCentavos % $quantidade;

        $saldoCentavos = $principalCentavos;
        $dataInicio = Carbon::parse($contrato->data_inicio);
        $totalReceberCentavos = 0;

        for ($i = 1; $i <= $quantidade; $i++) {
            $amortizacaoCentavos = $principalBase;
            $jurosCentavos = $jurosBase;

            // Ajusta os centavos restantes na última parcela.
            if ($i === $quantidade) {
                $amortizacaoCentavos += $principalResto;
                $jurosCentavos += $jurosResto;
            }

            $valorAtualCentavos = $amortizacaoCentavos + $jurosCentavos;

            $saldoCentavos = max(
                0,
                $saldoCentavos - $amortizacaoCentavos
            );

            Parcela::create([
                'contrato_id' => $contrato->id,
                'numero' => $i,
                'data_vencimento' => $dataInicio->copy()->addDays(
                    ($i - 1) * $intervalo
                ),
                'valor' => $valorAtualCentavos / 100,
                'juros_valor' => $jurosCentavos / 100,
                'amortizacao' => $amortizacaoCentavos / 100,
                'saldo_devedor' => $saldoCentavos / 100,
                'status' => false,
            ]);

            $totalReceberCentavos += $valorAtualCentavos;
        }

        $totalReceber = $totalReceberCentavos / 100;

        $contrato->update([
            'total_receber' => $totalReceber,
            'lucro' => round(
                $totalReceber - (float) $contrato->valor,
                2
            ),
            'valor_parcela' => round(
                ($totalReceberCentavos / $quantidade) / 100,
                2
            ),
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
