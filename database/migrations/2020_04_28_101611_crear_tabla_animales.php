<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAnimales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('animales', function (Blueprint $table) {
//            $table->increments('id')->unsigned();
//            $table->integer('idParticularidades')->unsigned();
//            $table->string('nombre')->nullable();
//            $table->string('sexo');
//            $table->integer('edadAproximada')->nullable();
//            $table->boolean('castrado');
//            $table->string('tamanio')->nullable();
//            $table->string('celularDuenio')->nullable();
//            $table->string('telefonoDuenio')->nullable();
//            $table->string('emailDuenio')->nullable();
//            $table->integer('idCreador')->unsigned();;
//            $table->integer('ultUsuarioMdf')->unsigned();;
//            $table->timestamp('ultHoraMdf')->nullable();
//
//            $table->foreign('idCreador')->references('id')->on('usuarios');
//            $table->foreign('ultUsuarioMdf')->references('id')->on('usuarios');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('animales');
    }
}
