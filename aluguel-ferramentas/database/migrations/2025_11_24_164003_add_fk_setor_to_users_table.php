<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // A foreign key de setor_id já foi criada
        // na migration 2025_11_24_132005_add_campos_to_users_table.
    }

    public function down(): void
    {
        // Não remover a foreign key aqui,
        // pois ela pertence à migration anterior.
    }
};