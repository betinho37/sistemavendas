<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained()->onDelete('cascade');
            $table->integer('numero'); // Número da parcela
            $table->date('data_vencimento'); // Data do vencimento da parcela
            $table->decimal('valor', 10, 2); // Valor da parcela
            $table->boolean('status')->default(0);
            // Indica se a parcela foi paga ou não
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
