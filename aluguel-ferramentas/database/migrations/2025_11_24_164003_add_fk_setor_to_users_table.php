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
    Schema::table('users', function (Blueprint $table) {
        // Se o setor_id existir e ainda não tiver FK:
        $table->foreign('setor_id')
              ->references('id')
              ->on('setores')
              ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['setor_id']);
    });
}

};
