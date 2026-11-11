<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EdnaMariaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $agora = now();
            $cpf = '706.820.971-87';

            // 1. Cadastrar ou localizar a cliente
            $cliente = DB::table('clientes')
                ->where('cpf', $cpf)
                ->first();

            if (!$cliente) {
                $clienteId = DB::table('clientes')->insertGetId([
                    'nome' => 'Edna Maria',
                    'endereco' => 'Não informado',
                    'fone' => '(61) 99230-7007',
                    'cpf' => $cpf,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);

                echo "Cliente cadastrada: Edna Maria\n";
            } else {
                $clienteId = $cliente->id;
                echo "Cliente já cadastrada. ID: {$clienteId}\n";
            }

            // 2. Verificar se o contrato já existe
            $contrato = DB::table('contratos')
                ->where('cliente_id', $clienteId)
                ->whereDate('data_inicio', '2026-10-10')
                ->where('valor', 4000)
                ->where('qtd_parcelas', 18)
                ->first();

            if (!$contrato) {
                $ultimoNumero = DB::table('contratos')->max('numero') ?? 0;

                $contratoId = DB::table('contratos')->insertGetId([
                    'numero' => $ultimoNumero + 1,
                    'numero_parcelas' => 18,
                    'cliente_id' => $clienteId,
                    'valor' => 4000,
                    'qtd_parcelas' => 18,
                    'intervalo_dias' => 30,
                    'juros' => 7.76925,
                    'data_inicio' => '2026-10-10',
                    'observacao' => 'Empréstimo de R$ 4.000,00 em 18 parcelas de R$ 420,00.',
                    'vencimento_sabado' => false,
                    'vencimento_domingo' => false,
                    'total_receber' => 7560,
                    'lucro' => 3560,
                    'valor_parcela' => 420,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);

                echo "Contrato cadastrado. ID: {$contratoId}\n";
            } else {
                $contratoId = $contrato->id;
                echo "Contrato já existe. ID: {$contratoId}\n";
            }

            // 3. Criar as 18 parcelas com vencimento todo dia 12
            for ($numero = 1; $numero <= 18; $numero++) {

                $vencimento = Carbon::create(2026, 11, 12)
                    ->addMonthsNoOverflow($numero - 1);

                $parcela = DB::table('parcelas')
                    ->where('contrato_id', $contratoId)
                    ->where('numero', $numero)
                    ->first();

                if (!$parcela) {
                    $parcelaId = DB::table('parcelas')->insertGetId([
                        'contrato_id' => $contratoId,
                        'numero' => $numero,
                        'data_vencimento' => $vencimento->toDateString(),
                        'valor' => 420,
                        'status' => $numero === 1,
                        'created_at' => $agora,
                        'updated_at' => $agora,
                    ]);
                } else {
                    $parcelaId = $parcela->id;
                }

                // 4. Registrar o primeiro pagamento recebido em 10/11/2026
                if ($numero === 1) {

                    if (!Schema::hasTable('pagamentos')) {
                        throw new \RuntimeException(
                            'A tabela pagamentos não existe. Execute php artisan migrate.'
                        );
                    }

                    $existePagamento = DB::table('pagamentos')
                        ->where('parcela_id', $parcelaId)
                        ->whereDate('data_pagamento', '2026-11-10')
                        ->where('valor', 420)
                        ->exists();

                    if (!$existePagamento) {
                        DB::table('pagamentos')->insert([
                            'parcela_id' => $parcelaId,
                            'user_id' => null,
                            'valor' => 420,
                            'data_pagamento' => '2026-11-10',
                            'observacao' => 'Primeiro pagamento recebido.',
                            'created_at' => $agora,
                            'updated_at' => $agora,
                        ]);
                    }

                    DB::table('parcelas')
                        ->where('id', $parcelaId)
                        ->update([
                            'status' => true,
                            'updated_at' => $agora,
                        ]);
                }
            }

            echo "18 parcelas verificadas.\n";
            echo "Cadastro da Edna finalizado!\n";
        });
    }
}
