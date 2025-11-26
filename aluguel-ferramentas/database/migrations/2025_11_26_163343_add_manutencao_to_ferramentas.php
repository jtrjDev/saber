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
    Schema::table('ferramentas', function (Blueprint $table) {
        $table->boolean('em_manutencao')->default(false);
        $table->date('data_manutencao')->nullable();
        $table->date('previsao_retorno')->nullable();
        $table->unsignedBigInteger('responsavel_manutencao')->nullable();
        $table->decimal('custo_manutencao', 10, 2)->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ferramentas', function (Blueprint $table) {
            //
        });
    }
};
