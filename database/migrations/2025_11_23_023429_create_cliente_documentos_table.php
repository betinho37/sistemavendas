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
    Schema::create('cliente_documentos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('cliente_id');
        $table->string('nome');
        $table->string('arquivo');
        $table->timestamps();

        $table->foreign('cliente_id')
              ->references('id')
              ->on('clientes')
              ->onDelete('cascade'); 
    });
}

public function down()
{
    Schema::dropIfExists('cliente_documentos');
}

};
