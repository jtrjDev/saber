<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/2025_01_01_000000_create_contratos_table.php
public function up()
{
    Schema::create('contratos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('aluguel_id')->constrained('alugueis')->onDelete('cascade');

        // Número do contrato tipo: 2025-000001
        $table->string('numero')->unique();

        // Versão do contrato (para histórico)
        $table->unsignedInteger('versao')->default(1);

        // Caminho do PDF gerado
        $table->string('arquivo_pdf')->nullable();

        // Data de assinatura
        $table->date('data_assinatura')->nullable();

        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
