<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mantenimientos_mobiliarios', function (Blueprint $table): void {
            if (! Schema::hasColumn('mantenimientos_mobiliarios', 'es_area_comun')) {
                $table->boolean('es_area_comun')->default(false)->after('estado_fisico_anterior');
            }
        });

        Schema::table('mantenimientos_dispositivos', function (Blueprint $table): void {
            if (! Schema::hasColumn('mantenimientos_dispositivos', 'es_area_comun')) {
                $table->boolean('es_area_comun')->default(false)->after('estado_fisico_anterior');
            }
        });

        // Backfill de es_area_comun en mantenimientos a partir de sus asignaciones de área común
        DB::statement('UPDATE mantenimientos_dispositivos m
            JOIN asignacions a ON a.id = m.asignacion_id
            SET m.es_area_comun = 1
            WHERE a.es_area_comun = 1');

        DB::statement('UPDATE mantenimientos_mobiliarios m
            JOIN asignacions a ON a.id = m.asignacion_id
            SET m.es_area_comun = 1
            WHERE a.es_area_comun = 1');

        // Normalizar cualquier valor residual de 'Usado' que no tenga el nombre completo canónico
        if (Schema::hasTable('devoluciones')) {
            DB::table('devoluciones')
                ->where('estado_devuelto', 'Usado')
                ->update(['estado_devuelto' => 'Usado / Segunda mano']);

            if (Schema::hasColumn('devoluciones', 'estado_anterior')) {
                DB::table('devoluciones')
                    ->where('estado_anterior', 'Usado')
                    ->update(['estado_anterior' => 'Usado / Segunda mano']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos_mobiliarios', function (Blueprint $table): void {
            if (Schema::hasColumn('mantenimientos_mobiliarios', 'es_area_comun')) {
                $table->dropColumn('es_area_comun');
            }
        });

        Schema::table('mantenimientos_dispositivos', function (Blueprint $table): void {
            if (Schema::hasColumn('mantenimientos_dispositivos', 'es_area_comun')) {
                $table->dropColumn('es_area_comun');
            }
        });
    }
};
