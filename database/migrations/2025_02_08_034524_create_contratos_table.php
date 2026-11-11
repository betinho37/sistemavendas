<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('contratos', function (Blueprint $table) {
        $table->id();
        $table->integer('numero')->default(0);;
        $table->integer('numero_parcelas')->default(0);
        $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
        $table->decimal('valor', 10, 2);
        $table->integer('qtd_parcelas')->default(1);
        $table->integer('intervalo_dias');
        $table->decimal('juros', 5, 2)->default(0);
        $table->date('data_inicio')->nullable(); // Permite NULL
        $table->text('observacao')->nullable();
        $table->boolean('vencimento_sabado')->default(false);
        $table->boolean('vencimento_domingo')->default(false);
        $table->decimal('total_receber', 10, 2)->default(0);
        $table->decimal('lucro', 10, 2)->default(0);
        $table->decimal('valor_parcela', 10, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
