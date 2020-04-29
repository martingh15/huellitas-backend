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
        Schema::create('animales', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('idParticularidades');
            $table->string('nombre')->nullable();
            $table->string('sexo');
            $table->integer('edadAproximada')->nullable();
            $table->boolean('castrado');
            $table->string('tamanio')->nullable();
            $table->string('celularDuenio')->nullable();
            $table->string('telefonoDuenio')->nullable();
            $table->string('emailDuenio')->nullable();
            $table->unsignedInteger('idCreador');
            $table->unsignedInteger('ultUsuarioMdf');
            $table->timestamp('ultHoraMdf')->nullable();

            $table->foreign('idCreador')->references('id')->on('usuarios');
            $table->foreign('idParticularidades')->references('id')->on('animales_particularidades');
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
        Schema::dropIfExists('animales');
    }
}
