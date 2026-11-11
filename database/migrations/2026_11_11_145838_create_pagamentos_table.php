
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parcela_id')
                ->constrained('parcelas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->text('observacao')->nullable();

            $table->timestamps();

            $table->index(['parcela_id', 'data_pagamento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
