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
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Columna virtual y clave única para mantenimientos de mobiliario (máximo 1 en_proceso por mueble)
            DB::statement("ALTER TABLE mantenimientos_mobiliarios 
                ADD COLUMN active_mobiliario_id BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN estado = 'en_proceso' THEN mobiliario_id ELSE NULL END) VIRTUAL,
                ADD UNIQUE KEY uq_mantenimientos_mob_active (active_mobiliario_id)");

            // Columna virtual y clave única para mantenimientos de dispositivos (máximo 1 en_proceso por dispositivo)
            DB::statement("ALTER TABLE mantenimientos_dispositivos 
                ADD COLUMN active_dispositivo_id BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN estado = 'en_proceso' THEN dispositivo_id ELSE NULL END) VIRTUAL,
                ADD UNIQUE KEY uq_mantenimientos_disp_active (active_dispositivo_id)");
        } elseif ($driver === 'sqlite') {
            DB::statement("ALTER TABLE mantenimientos_mobiliarios 
                ADD COLUMN active_mobiliario_id INTEGER GENERATED ALWAYS AS (CASE WHEN estado = 'en_proceso' THEN mobiliario_id ELSE NULL END)");
            DB::statement('CREATE UNIQUE INDEX uq_mantenimientos_mob_active ON mantenimientos_mobiliarios (active_mobiliario_id)');

            DB::statement("ALTER TABLE mantenimientos_dispositivos 
                ADD COLUMN active_dispositivo_id INTEGER GENERATED ALWAYS AS (CASE WHEN estado = 'en_proceso' THEN dispositivo_id ELSE NULL END)");
            DB::statement('CREATE UNIQUE INDEX uq_mantenimientos_disp_active ON mantenimientos_dispositivos (active_dispositivo_id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE mantenimientos_mobiliarios 
                DROP INDEX uq_mantenimientos_mob_active, 
                DROP COLUMN active_mobiliario_id');

            DB::statement('ALTER TABLE mantenimientos_dispositivos 
                DROP INDEX uq_mantenimientos_disp_active, 
                DROP COLUMN active_dispositivo_id');
        } elseif ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS uq_mantenimientos_mob_active');
            DB::statement('ALTER TABLE mantenimientos_mobiliarios DROP COLUMN active_mobiliario_id');
            DB::statement('DROP INDEX IF EXISTS uq_mantenimientos_disp_active');
            DB::statement('ALTER TABLE mantenimientos_dispositivos DROP COLUMN active_dispositivo_id');
        }
    }
};
