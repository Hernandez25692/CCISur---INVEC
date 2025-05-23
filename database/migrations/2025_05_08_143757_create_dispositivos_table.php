<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('marca');
            $table->string('modelo');
            $table->string('n_serie')->unique();
            $table->string('ubicacion');
            $table->enum('estado', [
                'Nuevo / En perfectas condiciones',
                'Con pequeños detalles / Imperfecciones leves',
                'Usado / Segunda mano',
                'Dañado / Defectuoso',
                'En reparación / En revisión',
                'Producto incompleto',
                'Caducado / No apto para uso'
            ]);
            $table->enum('disponibilidad', [
                'Asignado',
                'Sin Asignar',
                'No Aplica para asignación'
            ])->default('Sin Asignar');

            $table->string('etiqueta')->unique()->nullable();
            $table->date('fecha_registro')->default(now());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
