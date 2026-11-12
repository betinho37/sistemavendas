<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->decimal('juros_valor', 12, 2)
                ->nullable()
                ->after('valor');

            $table->decimal('amortizacao', 12, 2)
                ->nullable()
                ->after('juros_valor');

            $table->decimal('saldo_devedor', 12, 2)
                ->nullable()
                ->after('amortizacao');
        });
    }

    public function down(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->dropColumn([
                'juros_valor',
                'amortizacao',
                'saldo_devedor',
            ]);
        });
    }
};
