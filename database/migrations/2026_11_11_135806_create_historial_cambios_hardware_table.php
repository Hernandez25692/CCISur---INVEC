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
        Schema::create('historial_cambios_hardware', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asignacion_id')->nullable()->constrained('asignacions')->nullOnDelete();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->json('especificaciones_anteriores')->nullable();
            $table->json('especificaciones_nuevas')->nullable();
            $table->json('cambios'); // Arreglo estructurado de cambios
            $table->string('motivo', 500)->nullable();
            $table->boolean('revertido')->default(false);
            $table->foreignId('revertido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revertido_at')->nullable();
            $table->string('motivo_reversion', 500)->nullable();
            $table->timestamps();

            $table->index(['dispositivo_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cambios_hardware');
    }
};
