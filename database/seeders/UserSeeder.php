<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Verifica si ya existe
        if (! User::where('email', 'admin@ccisur.hn')->exists()) {
            User::create([
                'name' => 'Super Administrador',
                'email' => 'admin@ccisur.hn',
                'password' => Hash::make(env('SEEDER_ADMIN_PASSWORD', 'admin1234')),
                'rol' => User::ROL_SUPERADMIN,
                'activo' => true,
                'debe_cambiar_password' => true, // Exige cambiar la clave por defecto al entrar por primera vez
            ]);
        }
    }
}
