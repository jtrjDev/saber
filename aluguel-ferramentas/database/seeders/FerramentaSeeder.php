<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ferramenta;

class FerramentaSeeder extends Seeder
{
    public function run(): void
    {
        $ferramentas = [
            [
                'nome' => 'Furadeira Bosch 600W',
                'descricao' => 'Furadeira profissional',
                'estado' => 'boa',
                'setor_id' => 1,
            ],
            [
                'nome' => 'Marreta 5kg',
                'descricao' => 'Marreta reforçada',
                'estado' => 'boa',
                'setor_id' => 1,
            ],
            [
                'nome' => 'Alicate Universal',
                'descricao' => 'Alicate para uso geral',
                'estado' => 'boa',
                'setor_id' => 1,
            ],
            [
                'nome' => 'Chave Philips',
                'descricao' => 'Chave de fenda tipo philips',
                'estado' => 'boa',
                'setor_id' => 1,
            ],
            [
                'nome' => 'Extensão 20 metros',
                'descricao' => 'Cabo elétrico',
                'estado' => 'boa',
                'setor_id' => 1,
            ],
        ];

        foreach ($ferramentas as $f) {
            Ferramenta::create($f);
        }
    }
}
