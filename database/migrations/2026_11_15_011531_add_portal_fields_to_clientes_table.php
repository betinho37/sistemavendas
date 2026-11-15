
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('portal_token', 64)->nullable()->unique();
            $table->boolean('portal_ativo')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['portal_token']);
            $table->dropColumn(['portal_token', 'portal_ativo']);
        });
    }
};
