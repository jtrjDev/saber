<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Master',
            'email' => 'admin@email.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'setor_id' => null,
        ]);
    }
}
