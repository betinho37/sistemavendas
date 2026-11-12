<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagamentoController extends Controller
{
    public function store(Request $request, Parcela $parcela)
    {
        $dados = $request->validate([
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_pagamento' => ['required', 'date'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);

        return DB::transaction(function () use ($parcela, $dados) {
            $parcela = Parcela::whereKey($parcela->id)
                ->lockForUpdate()
                ->firstOrFail();

            $user = Auth::user();

            if (!$user->isAdmin()) {
                $permitida = Parcela::whereKey($parcela->id)
                    ->whereHas('contrato.cliente', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->exists();

                abort_unless($permitida, 403);
            }

            $totalPago = (float) $parcela->pagamentos()->sum('valor');
            $valorParcela = (float) $parcela->valor;
            $saldo = round($valorParcela - $totalPago, 2);
            $valorPagamento = round((float) $dados['valor'], 2);

            if ($parcela->status && $totalPago == 0) {
                throw ValidationException::withMessages([
                    'valor' => 'Esta parcela já estava marcada como paga e não possui histórico de recebimentos.',
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

            $parcela->pagamentos()->create([
                'user_id' => $user->id,
                'valor' => $valorPagamento,
                'data_pagamento' => $dados['data_pagamento'],
                'observacao' => $dados['observacao'] ?? null,
            ]);

            $novoTotal = (float) $parcela->pagamentos()->sum('valor');

            $parcela->update([
                'status' => $novoTotal >= $valorParcela,
            ]);

            return back()->with(
                'success',
                'Pagamento de R$ ' . number_format($valorPagamento, 2, ',', '.') . ' registrado com sucesso!'
            );
        });
    }
}
