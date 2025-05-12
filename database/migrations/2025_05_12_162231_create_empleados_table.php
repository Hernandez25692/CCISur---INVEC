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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // EM001
            $table->string('identidad')->unique();
            $table->string('nombre_completo');
            $table->enum('gerencia', [
                'Dirección Ejecutiva',
                'Gerencia Administrativa y Financiera',
                'Gerencia de Operaciones Registrales',
                'Gerencia de Servicios Empresariales y Afiliaciones'
            ]);
            $table->enum('ubicacion', ['Choluteca', 'Valle']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
