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
        Schema::create('alugueis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // quem alugou
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete(); // quem entregou
            $table->foreignId('casa_id')->constrained()->cascadeOnDelete(); // qual congregação
            $table->date('data_retirada');
            $table->date('data_prevista');
            $table->date('data_devolucao')->nullable();
            $table->string('status')->default('aberto'); // aberto / devolvido / atraso
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alugueis');
    }
};
