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
        Schema::create('asignacions', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['mobiliario', 'dispositivo']);
            $table->unsignedBigInteger('id_referencia');
            $table->string('colaborador');
            $table->string('area');
            $table->text('observaciones')->nullable();
            $table->date('fecha_entrega');
            $table->string('entregado_por');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacions');
    }
};
