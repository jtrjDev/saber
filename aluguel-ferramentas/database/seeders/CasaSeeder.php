<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Casa;

class CasaSeeder extends Seeder
{
    public function run(): void
    {
        $casas = [

            // SETOR 1
            ['nome' => 'Monte Carlo', 'setor_id' => 1],
            ['nome' => 'Casoni', 'setor_id' => 1],

            // SETOR 2
            ['nome' => 'Cafezal', 'setor_id' => 2],
            ['nome' => 'Jd Piza', 'setor_id' => 2],
            ['nome' => 'Franciscato', 'setor_id' => 2],

            // SETOR 3
            ['nome' => 'Novo Bandeirantes', 'setor_id' => 3],
            ['nome' => 'Cambé', 'setor_id' => 3],

            // SETOR 4
            ['nome' => 'Violim', 'setor_id' => 4],
            ['nome' => 'Heimtal', 'setor_id' => 4],

            // SETOR 5
            ['nome' => 'Central Ibiporã', 'setor_id' => 5],
            ['nome' => 'San Rafael', 'setor_id' => 5],
        ];

        foreach ($casas as $c) {
            Casa::create($c);
        }
    }
}
