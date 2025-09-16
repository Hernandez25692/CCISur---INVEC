<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asignacions', function (Blueprint $table) {
            $table->id();

            $table->string('tipo');
            $table->unsignedBigInteger('id_referencia');

            // FK -> empleados
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->restrictOnDelete(); // evita borrado accidental en cascada

            $table->string('area');
            $table->text('observaciones')->nullable();
            $table->date('fecha_entrega');
            $table->string('entregado_por');
            $table->uuid('uuid')->unique();
            $table->timestamps();

            // Índices útiles
            $table->index('id_referencia');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacions');
    }
};
