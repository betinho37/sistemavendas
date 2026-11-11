<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('endereco');
        $table->string('fone')->nullable();            
        $table->string('cpf')->nullable()->unique();
        $table->string('email')->nullable(); // retirar UNIQUE
        $table->date('data_nascimento')->nullable();
        $table->enum('genero', ['Masculino', 'Feminino', 'Outro'])->nullable();
        $table->enum('estado_civil', ['Solteiro', 'Casado'])->nullable();
        $table->string('foto')->nullable(); // adicionar
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};

