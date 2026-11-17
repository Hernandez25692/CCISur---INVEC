<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalizar registros existentes en mobiliarios
        DB::table('mobiliarios')
            ->where('estado', 'Con pequeños detalles / Imperfecciones leves')
            ->update(['estado' => 'Usado / Segunda mano']);

        DB::table('mobiliarios')
            ->whereIn('estado', ['Producto incompleto', 'Caducado / No apto para uso'])
            ->update(['estado' => 'Dañado / Defectuoso']);

        // 2. Normalizar registros existentes en dispositivos
        DB::table('dispositivos')
            ->where('estado', 'Con pequeños detalles / Imperfecciones leves')
            ->update(['estado' => 'Usado / Segunda mano']);

        DB::table('dispositivos')
            ->whereIn('estado', ['Producto incompleto', 'Caducado / No apto para uso'])
            ->update(['estado' => 'Dañado / Defectuoso']);

        // 3. Normalizar registros existentes en devoluciones
        DB::table('devoluciones')
            ->where('estado_devuelto', 'Con pequeños detalles / Imperfecciones leves')
            ->update(['estado_devuelto' => 'Usado / Segunda mano']);

        DB::table('devoluciones')
            ->whereIn('estado_devuelto', ['Producto incompleto', 'Caducado / No apto para uso'])
            ->update(['estado_devuelto' => 'Dañado / Defectuoso']);

        if (Schema::hasColumn('devoluciones', 'estado_anterior')) {
            DB::table('devoluciones')
                ->where('estado_anterior', 'Con pequeños detalles / Imperfecciones leves')
                ->update(['estado_anterior' => 'Usado / Segunda mano']);

            DB::table('devoluciones')
                ->whereIn('estado_anterior', ['Producto incompleto', 'Caducado / No apto para uso'])
                ->update(['estado_anterior' => 'Dañado / Defectuoso']);
        }

        // 4. Normalizar registros existentes en mantenimientos_dispositivos
        if (Schema::hasTable('mantenimientos_dispositivos')) {
            DB::table('mantenimientos_dispositivos')
                ->where('estado_fisico_anterior', 'Con pequeños detalles / Imperfecciones leves')
                ->update(['estado_fisico_anterior' => 'Usado / Segunda mano']);

            DB::table('mantenimientos_dispositivos')
                ->whereIn('estado_fisico_anterior', ['Producto incompleto', 'Caducado / No apto para uso'])
                ->update(['estado_fisico_anterior' => 'Dañado / Defectuoso']);

            DB::table('mantenimientos_dispositivos')
                ->where('estado_fisico_resultante', 'Con pequeños detalles / Imperfecciones leves')
                ->update(['estado_fisico_resultante' => 'Usado / Segunda mano']);

            DB::table('mantenimientos_dispositivos')
                ->whereIn('estado_fisico_resultante', ['Producto incompleto', 'Caducado / No apto para uso'])
                ->update(['estado_fisico_resultante' => 'Dañado / Defectuoso']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo: los 4 estados son un subconjunto válido de los estados previos.
    }
};
