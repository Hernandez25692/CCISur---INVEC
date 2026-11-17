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
        Schema::create('mantenimientos_mobiliarios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mobiliario_id')->constrained('mobiliarios')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asignacion_id')->nullable()->constrained('asignacions')->nullOnDelete();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('tipo', 100);
            $table->text('motivo_falla');
            $table->string('taller_proveedor', 255)->nullable();
            $table->string('estado', 50)->default('en_proceso'); // 'en_proceso', 'completado_reparado', 'completado_irreparable', 'cancelado'
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->text('solucion_aplicada')->nullable();
            $table->string('estado_fisico_anterior', 100)->nullable();
            $table->string('estado_fisico_resultante', 100)->nullable();
            $table->foreignId('devolucion_id')->nullable()->constrained('devoluciones')->nullOnDelete();
            $table->timestamps();

            $table->index(['mobiliario_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos_mobiliarios');
    }
};
