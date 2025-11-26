<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setor;

class SetorSeeder extends Seeder
{
    public function run(): void
    {
        $setores = [
            ['nome' => 'Casoni'],              // 1
            ['nome' => 'Cafezal'],             // 2
            ['nome' => 'Novo Bandeirantes'],   // 3
            ['nome' => 'Heimtal'],             // 4
            ['nome' => 'Ibiporã'],             // 5
        ];

        foreach ($setores as $s) {
            Setor::create($s);
        }
    }
}
