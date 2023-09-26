<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filmes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 328);
            $table->text('descricao');
            $table->string('categoria', 100);
            $table->text('resumo');
            $table->text('urlimg');
            $table->unsignedBigInteger('fk_ator_principal');
            $table->foreign('fk_ator_principal')->references('id')->on('atores');
            $table->unsignedBigInteger('fk_diretor');
            $table->foreign('fk_diretor')->references('id')->on('diretores');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filmes');
    }
};
