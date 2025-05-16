<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Support\Str;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            'Nuevo / En perfectas condiciones',
            'Con pequeños detalles / Imperfecciones leves',
            'Usado / Segunda mano',
            'Dañado / Defectuoso',
            'En reparación / En revisión',
            'Producto incompleto',
            'Caducado / No apto para uso'
        ];

        $disponibilidades = [
            'Asignado',
            'Sin Asignar',
            'No Aplica para asignación'
        ];

        $ubicaciones = ['Oficina A', 'Oficina B', 'Almacén', 'Sala de Reuniones'];

        // Crear 100 dispositivos
        for ($i = 1; $i <= 100; $i++) {
            Dispositivo::create([
                'nombre' => "Dispositivo $i",
                'tipo' => fake()->randomElement(['Laptop', 'PC', 'Proyector', 'Impresora']),
                'marca' => fake()->randomElement(['HP', 'Dell', 'Lenovo', 'Epson']),
                'modelo' => 'Modelo-' . fake()->bothify('??###'),
                'n_serie' => 'DS-' . strtoupper(Str::random(8)),
                'ubicacion' => fake()->randomElement($ubicaciones),
                'estado' => fake()->randomElement($estados),
                'disponibilidad' => fake()->randomElement($disponibilidades),
                'fecha_registro' => now(),
                'etiqueta' => 'ETQ-D' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
        }

        // Crear 100 mobiliarios
        for ($i = 1; $i <= 100; $i++) {
            Mobiliario::create([
                'nombre' => "Mobiliario $i",
                'tipo' => fake()->randomElement(['Silla', 'Mesa', 'Archivador', 'Estantería']),
                'ubicacion' => fake()->randomElement($ubicaciones),
                'estado' => fake()->randomElement($estados),
                'disponibilidad' => fake()->randomElement($disponibilidades),
                'fecha_registro' => now(),
                'etiqueta' => 'ETQ-M' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $this->command->info('✅ Se crearon 100 dispositivos y 100 mobiliarios.');
    }
}
