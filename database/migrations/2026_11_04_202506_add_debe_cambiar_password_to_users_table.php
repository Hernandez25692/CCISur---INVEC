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
            $table->boolean('debe_cambiar_password')->default(false)->after('activo')->index();
        });

        // Asegurar que el usuario institucional del seeder sea el único Superadministrador
        DB::table('users')->where('email', 'admin@ccisur.hn')->update([
            'rol' => 'superadmin',
            'debe_cambiar_password' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('debe_cambiar_password');
        });
    }
};
