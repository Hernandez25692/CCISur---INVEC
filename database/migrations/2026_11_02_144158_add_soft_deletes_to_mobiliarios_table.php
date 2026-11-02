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
        Schema::table('mobiliarios', function (Blueprint $table) {
            $table->softDeletes()->after('etiqueta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobiliarios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
