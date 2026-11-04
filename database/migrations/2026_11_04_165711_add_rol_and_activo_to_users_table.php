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
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 30)->default('lectura')->after('password')->index();
            $table->boolean('activo')->default(true)->after('rol')->index();
        });

        // Asegurar que todos los usuarios existentes en el sistema queden como administradores activos
        DB::table('users')->update([
            'rol' => 'administrador',
            'activo' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
        });
    }
};
