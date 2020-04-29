<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableZonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->unsignedInteger('idCreador');
            $table->unsignedInteger('ultUsuarioMdf');

            $table->foreign('idCreador')->references('id')->on('usuarios');
            $table->foreign('ultUsuarioMdf')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas');
    }
}
