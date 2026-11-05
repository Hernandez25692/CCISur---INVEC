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
        Schema::table('asignacions', function (Blueprint $table) {
            $table->softDeletes()->after('entregado_por');
            $table->foreignId('anulado_por_user_id')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable()->after('anulado_por_user_id');
        });

        Schema::table('devoluciones', function (Blueprint $table) {
            $table->softDeletes()->after('observaciones');
            $table->foreignId('anulado_por_user_id')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable()->after('anulado_por_user_id');

            // Crear índice ordinario para satisfacer la clave foránea antes de remover el índice único
            $table->index('asignacion_id', 'devoluciones_asignacion_id_index');
            $table->dropUnique('devoluciones_asignacion_id_unique');
        });

        // Columna virtual y clave única para garantizar que solo exista una devolución ACTIVA por asignación
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE devoluciones ADD COLUMN active_asignacion_id BIGINT GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN asignacion_id ELSE NULL END) VIRTUAL, ADD UNIQUE KEY uq_devoluciones_active_asignacion (active_asignacion_id)');
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE devoluciones ADD COLUMN active_asignacion_id INTEGER GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN asignacion_id ELSE NULL END)');
            DB::statement('CREATE UNIQUE INDEX uq_devoluciones_active_asignacion ON devoluciones (active_asignacion_id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE devoluciones DROP KEY IF EXISTS uq_devoluciones_active_asignacion, DROP COLUMN IF EXISTS active_asignacion_id');
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS uq_devoluciones_active_asignacion');
        }

        Schema::table('devoluciones', function (Blueprint $table) {
            $table->unique('asignacion_id', 'devoluciones_asignacion_id_unique');
            $table->dropIndex('devoluciones_asignacion_id_index');
            $table->dropForeign(['anulado_por_user_id']);
            $table->dropColumn(['motivo_anulacion', 'anulado_por_user_id', 'deleted_at']);
        });

        Schema::table('asignacions', function (Blueprint $table) {
            $table->dropForeign(['anulado_por_user_id']);
            $table->dropColumn(['motivo_anulacion', 'anulado_por_user_id', 'deleted_at']);
        });
    }
};
