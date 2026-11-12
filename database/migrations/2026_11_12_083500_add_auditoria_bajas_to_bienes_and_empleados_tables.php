<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dispositivos', function (Blueprint $table): void {
            $table->string('motivo_baja', 500)->nullable()->after('deleted_at');
            $table->foreignId('baja_por_user_id')->nullable()->after('motivo_baja')->constrained('users')->nullOnDelete();
            $table->foreignId('reactivado_por_user_id')->nullable()->after('baja_por_user_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('mobiliarios', function (Blueprint $table): void {
            $table->string('motivo_baja', 500)->nullable()->after('deleted_at');
            $table->foreignId('baja_por_user_id')->nullable()->after('motivo_baja')->constrained('users')->nullOnDelete();
            $table->foreignId('reactivado_por_user_id')->nullable()->after('baja_por_user_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('empleados', function (Blueprint $table): void {
            $table->foreignId('baja_por_user_id')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            $table->foreignId('reactivado_por_user_id')->nullable()->after('baja_por_user_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositivos', function (Blueprint $table): void {
            $table->dropForeign(['baja_por_user_id']);
            $table->dropForeign(['reactivado_por_user_id']);
            $table->dropColumn(['motivo_baja', 'baja_por_user_id', 'reactivado_por_user_id']);
        });

        Schema::table('mobiliarios', function (Blueprint $table): void {
            $table->dropForeign(['baja_por_user_id']);
            $table->dropForeign(['reactivado_por_user_id']);
            $table->dropColumn(['motivo_baja', 'baja_por_user_id', 'reactivado_por_user_id']);
        });

        Schema::table('empleados', function (Blueprint $table): void {
            $table->dropForeign(['baja_por_user_id']);
            $table->dropForeign(['reactivado_por_user_id']);
            $table->dropColumn(['baja_por_user_id', 'reactivado_por_user_id']);
        });
    }
};
