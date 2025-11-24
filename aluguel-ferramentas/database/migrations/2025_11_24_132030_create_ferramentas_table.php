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
        Schema::create('ferramentas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('foto')->nullable();
            $table->string('estado')->default('bom'); // bom / ruim / manutenção / quebrado
            $table->decimal('valor_compra', 10, 2)->nullable();
            $table->text('descricao')->nullable();
            $table->foreignId('setor_id')
                    ->constrained('setores')
                    ->cascadeOnDelete();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ferramentas');
    }
};
