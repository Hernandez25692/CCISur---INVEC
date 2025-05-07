<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();

            // Primero definimos la columna
            $table->unsignedBigInteger('asignacion_id');

            // Luego la clave foránea
            $table->foreign('asignacion_id')->references('id')->on('asignacions')->onDelete('cascade');

            $table->date('fecha_devolucion');
            $table->string('recibido_por');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
