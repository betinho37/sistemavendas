<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        $faker = $this->faker;
        $faker->addProvider(new \Mmo\Faker\FakeBr($faker));

            return [
                'nome' => $faker->name(),
                'endereco' => $faker->address(),
                'fone' => $faker->unique()->phoneNumber(),
                'cpf' => $faker->unique()->numerify('###########'),
                'email' => $faker->unique()->safeEmail(),
                'data_nascimento' => $faker->date(),
                'genero' => $faker->randomElement(['Masculino', 'Feminino', 'Outro']),
                'estado_civil' => $faker->randomElement(['Solteiro', 'Casado']),
                'observacoes' => $faker->sentence(),
            ];
    }
}
