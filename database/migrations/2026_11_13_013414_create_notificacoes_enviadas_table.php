
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes_enviadas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parcela_id')
                ->constrained('parcelas')
                ->cascadeOnDelete();

            $table->string('canal', 20);
            $table->date('data_vencimento');
            $table->string('status', 20)->default('pendente');
            $table->timestamp('enviado_em')->nullable();
            $table->text('erro')->nullable();
            $table->timestamps();

            $table->unique(
                ['parcela_id', 'canal', 'data_vencimento'],
                'notificacoes_parcela_canal_vencimento_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_enviadas');
    }
};
