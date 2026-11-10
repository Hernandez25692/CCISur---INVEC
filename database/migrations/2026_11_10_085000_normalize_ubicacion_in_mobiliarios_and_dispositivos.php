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
        // Normalizar ubicaciones en mobiliarios hacia las sedes oficiales ['Choluteca', 'Valle']
        DB::table('mobiliarios')->cursor()->each(function ($item): void {
            $ubicacionActual = trim((string) $item->ubicacion);
            $nuevaUbicacion = strcasecmp($ubicacionActual, 'Valle') === 0 ? 'Valle' : 'Choluteca';

            if ($item->ubicacion !== $nuevaUbicacion) {
                DB::table('mobiliarios')->where('id', $item->id)->update(['ubicacion' => $nuevaUbicacion]);
            }
        });

        // Normalizar ubicaciones en dispositivos hacia las sedes oficiales ['Choluteca', 'Valle']
        DB::table('dispositivos')->cursor()->each(function ($item): void {
            $ubicacionActual = trim((string) $item->ubicacion);
            $nuevaUbicacion = strcasecmp($ubicacionActual, 'Valle') === 0 ? 'Valle' : 'Choluteca';

            if ($item->ubicacion !== $nuevaUbicacion) {
                DB::table('dispositivos')->where('id', $item->id)->update(['ubicacion' => $nuevaUbicacion]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo: las sedes normalizadas se mantienen válidas
    }
};
