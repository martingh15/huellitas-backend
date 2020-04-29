<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAnimalPerdidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animales_perdidos', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('idAnimal');
            $table->unsignedInteger('idZona');
            $table->timestamp('fechaPerdido');
            $table->unsignedInteger('idCreador');
            $table->unsignedInteger('ultUsuarioMdf');
            $table->timestamp('ultHoraMdf')->nullable();

            $table->foreign('idCreador')->references('id')->on('usuarios');
            $table->foreign('idAnimal')->references('id')->on('animales');
            $table->foreign('idZona')->references('id')->on('zonas');
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
        Schema::dropIfExists('animales_perdidos');
    }
}
