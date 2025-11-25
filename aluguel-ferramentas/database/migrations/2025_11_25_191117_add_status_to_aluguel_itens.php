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
    Schema::table('aluguel_itens', function (Blueprint $table) {
        $table->string('status')->default('emprestado');
        $table->date('data_devolucao_item')->nullable();
        $table->string('observacao_devolucao')->nullable();
    });
}

public function down()
{
    Schema::table('aluguel_itens', function (Blueprint $table) {
        $table->dropColumn(['status', 'data_devolucao_item', 'observacao_devolucao']);
    });
}

};
