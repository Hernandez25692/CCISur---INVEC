<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dispositivos') && Schema::hasColumn('dispositivos', 'n_serie')) {
            Schema::table('dispositivos', function (Blueprint $table): void {
                $table->string('n_serie')->nullable()->change();
            });

            DB::table('dispositivos')
                ->whereIn(DB::raw('UPPER(TRIM(n_serie))'), ['', 'N/A', 'NA', 'S/N', 'SN', 'SIN SERIE', '-'])
                ->update(['n_serie' => null]);
        }

        // Backfill de llaves foráneas en asignaciones históricas
        if (Schema::hasTable('asignacions')) {
            if (Schema::hasColumn('asignacions', 'mobiliario_id') && Schema::hasTable('mobiliarios')) {
                $idsMobiliario = DB::table('mobiliarios')->pluck('id');
                DB::table('asignacions')
                    ->where('tipo', 'mobiliario')
                    ->whereNull('mobiliario_id')
                    ->whereIn('id_referencia', $idsMobiliario)
                    ->update(['mobiliario_id' => DB::raw('id_referencia')]);
            }

            if (Schema::hasColumn('asignacions', 'dispositivo_id') && Schema::hasTable('dispositivos')) {
                $idsDispositivo = DB::table('dispositivos')->pluck('id');
                DB::table('asignacions')
                    ->where('tipo', 'dispositivo')
                    ->whereNull('dispositivo_id')
                    ->whereIn('id_referencia', $idsDispositivo)
                    ->update(['dispositivo_id' => DB::raw('id_referencia')]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('dispositivos') && Schema::hasColumn('dispositivos', 'n_serie')) {
            // Para revertir sin fallar por valores nulos, asignamos un valor único temporal a los NULL
            $sinSerie = DB::table('dispositivos')->whereNull('n_serie')->pluck('id');
            foreach ($sinSerie as $id) {
                DB::table('dispositivos')->where('id', $id)->update(['n_serie' => 'SIN-SERIE-'.$id]);
            }

            Schema::table('dispositivos', function (Blueprint $table): void {
                $table->string('n_serie')->nullable(false)->change();
            });
        }
    }
};
