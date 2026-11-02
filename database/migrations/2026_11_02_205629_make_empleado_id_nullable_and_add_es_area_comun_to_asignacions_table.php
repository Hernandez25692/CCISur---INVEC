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
        Schema::table('asignacions', function (Blueprint $table) {
            $table->unsignedBigInteger('empleado_id')->nullable()->change();
            $table->boolean('es_area_comun')->default(false)->after('empleado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacions', function (Blueprint $table) {
            $table->dropColumn('es_area_comun');
            $table->unsignedBigInteger('empleado_id')->nullable(false)->change();
        });
    }
};
