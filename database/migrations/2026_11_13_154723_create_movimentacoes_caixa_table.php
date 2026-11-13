<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_caixa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('tipo', ['entrada', 'saida']);

            $table->string('categoria', 100);

            $table->decimal('valor', 12, 2);

            $table->date('data');

            $table->text('descricao')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'data']);
            $table->index(['tipo', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_caixa');
    }
};
