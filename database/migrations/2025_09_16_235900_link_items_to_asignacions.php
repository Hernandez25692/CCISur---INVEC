<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('asignacions')) return;

        // 1) Agregar columnas si no existen
        Schema::table('asignacions', function (Blueprint $table) {
            if (!Schema::hasColumn('asignacions', 'mobiliario_id')) {
                $table->unsignedBigInteger('mobiliario_id')->nullable()->after('id_referencia');
                $table->index('mobiliario_id', 'asignacions_mobiliario_idx');
            }
            if (!Schema::hasColumn('asignacions', 'dispositivo_id')) {
                $table->unsignedBigInteger('dispositivo_id')->nullable()->after('mobiliario_id');
                $table->index('dispositivo_id', 'asignacions_dispositivo_idx');
            }
        });

        // 2) Backfill (opcional: solo si usas tipo + id_referencia)
        if (Schema::hasColumn('asignacions', 'mobiliario_id')) {
            DB::statement("
                UPDATE asignacions
                SET mobiliario_id = id_referencia
                WHERE LOWER(tipo) IN ('mobiliario','mueble')
                  AND mobiliario_id IS NULL
            ");
            if (Schema::hasTable('mobiliarios')) {
                DB::statement("
                    UPDATE asignacions a
                    LEFT JOIN mobiliarios m ON m.id = a.mobiliario_id
                    SET a.mobiliario_id = NULL
                    WHERE a.mobiliario_id IS NOT NULL AND m.id IS NULL
                ");
            }
        }

        if (Schema::hasColumn('asignacions', 'dispositivo_id')) {
            DB::statement("
                UPDATE asignacions
                SET dispositivo_id = id_referencia
                WHERE LOWER(tipo) IN ('dispositivo','equipo','electronico','electrónico')
                  AND dispositivo_id IS NULL
            ");
            if (Schema::hasTable('dispositivos')) {
                DB::statement("
                    UPDATE asignacions a
                    LEFT JOIN dispositivos d ON d.id = a.dispositivo_id
                    SET a.dispositivo_id = NULL
                    WHERE a.dispositivo_id IS NOT NULL AND d.id IS NULL
                ");
            }
        }

        // 3) FKs
        Schema::table('asignacions', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('asignacions', 'mobiliario_id') && Schema::hasTable('mobiliarios')) {
                    $table->foreign('mobiliario_id', 'asignacions_mobiliario_fk')
                        ->references('id')->on('mobiliarios')
                        ->restrictOnDelete();
                }
            } catch (\Throwable $e) {
            }

            try {
                if (Schema::hasColumn('asignacions', 'dispositivo_id') && Schema::hasTable('dispositivos')) {
                    $table->foreign('dispositivo_id', 'asignacions_dispositivo_fk')
                        ->references('id')->on('dispositivos')
                        ->restrictOnDelete();
                }
            } catch (\Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('asignacions')) return;

        try {
            Schema::table('asignacions', fn(Blueprint $t) => $t->dropForeign('asignacions_mobiliario_fk'));
        } catch (\Throwable $e) {
        }
        try {
            Schema::table('asignacions', fn(Blueprint $t) => $t->dropForeign('asignacions_dispositivo_fk'));
        } catch (\Throwable $e) {
        }

        Schema::table('asignacions', function (Blueprint $table) {
            try {
                $table->dropIndex('asignacions_mobiliario_idx');
            } catch (\Throwable $e) {
            }
            try {
                $table->dropIndex('asignacions_dispositivo_idx');
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('asignacions', 'mobiliario_id')) $table->dropColumn('mobiliario_id');
            if (Schema::hasColumn('asignacions', 'dispositivo_id')) $table->dropColumn('dispositivo_id');
        });
    }
};
