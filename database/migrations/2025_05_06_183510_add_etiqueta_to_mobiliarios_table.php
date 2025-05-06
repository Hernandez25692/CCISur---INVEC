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
        Schema::table('mobiliarios', function (Blueprint $table) {
            $table->string('etiqueta')->unique()->nullable();
        });
    }

    public function down()
    {
        Schema::table('mobiliarios', function (Blueprint $table) {
            $table->dropColumn('etiqueta');
        });
    }
};
