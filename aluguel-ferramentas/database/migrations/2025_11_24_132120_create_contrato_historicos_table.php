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
// database/migrations/2025_01_01_000001_create_contrato_itens_table.php
// database/migrations/2025_01_01_000002_create_contrato_historicos_table.php
public function up()
{
    Schema::create('contrato_historicos', function (Blueprint $table) {
        $table->id();

        $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
        $table->string('acao');
        $table->text('detalhes')->nullable();

        $table->timestamps();
    });
}




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_historicos');
    }
};
