<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Verifica si ya existe
        if (!User::where('email', 'admin@ccisur.hn')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@ccisur.hn',
                'password' => Hash::make('admin1234'),
                
            ]);
        }
    }
}
