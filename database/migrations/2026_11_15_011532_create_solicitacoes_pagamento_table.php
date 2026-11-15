
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_pagamento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parcela_id')
                ->constrained('parcelas')
                ->cascadeOnDelete();

            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->string('comprovante');
            $table->string('status', 20)->default('pendente');

            $table->text('observacao')->nullable();
            $table->text('motivo_rejeicao')->nullable();

            $table->foreignId('aprovado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('aprovado_em')->nullable();

            $table->timestamps();

            $table->index(['parcela_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_pagamento');
    }
};
