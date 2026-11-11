<?php


namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Cliente::create([
                'nome' => "Cliente Teste {$i}",
                'endereco' => "Rua de Teste, {$i}",
                'fone' => sprintf('(61) 9%04d-%04d', $i, 1000 + $i),
                'email' => "cliente{$i}@exemplo.com",
                'cpf' => null,
                'observacoes' => 'Cliente cadastrado para testes.',
            ]);
        }
    }
}
