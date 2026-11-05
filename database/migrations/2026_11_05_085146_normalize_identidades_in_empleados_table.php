<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalizar todas las identidades existentes a 13 dígitos puros (sin guiones ni espacios)
        DB::table('empleados')->cursor()->each(function ($empleado) {
            $limpia = (string) preg_replace('/[\s\-]+/', '', trim((string) $empleado->identidad));
            if ($limpia !== '' && $limpia !== $empleado->identidad) {
                DB::table('empleados')->where('id', $empleado->id)->update(['identidad' => $limpia]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a formato con guiones si es un DNI hondureño de 13 dígitos
        DB::table('empleados')->cursor()->each(function ($empleado) {
            $limpia = (string) $empleado->identidad;
            if (preg_match('/^\d{13}$/', $limpia)) {
                $conGuiones = substr($limpia, 0, 4) . '-' . substr($limpia, 4, 4) . '-' . substr($limpia, 8, 5);
                DB::table('empleados')->where('id', $empleado->id)->update(['identidad' => $conGuiones]);
            }
        });
    }
};
