<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAnimalEncontrados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('animales_encontrados', function (Blueprint $table) {
//            $table->integer('id')->unsigned();;
//            $table->integer('idAnimal')->unsigned();;
//            $table->integer('idZona')->unsigned();;
//            $table->timestamp('fechaEncontrado');
//            $table->string('celularPersona')->nullable();
//            $table->string('telefonoPersona')->nullable();
//            $table->string('emailPersona')->nullable();
//            $table->integer('idCreador')->unsigned();;
//            $table->integer('ultUsuarioMdf')->unsigned();;
//            $table->timestamp('ultHoraMdf')->nullable();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('animales_encontrados');
    }
}
