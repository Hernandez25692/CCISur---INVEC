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
            $table->enum('estado', ['bueno', 'regular', 'dañado']);
            $table->enum('disponibilidad', ['disponible', 'asignado', 'inactivo'])->default('disponible');
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
