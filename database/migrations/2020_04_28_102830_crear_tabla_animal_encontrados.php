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
        Schema::create('animales_encontrados', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->bigInteger('idUsuario');
            $table->bigInteger('idAnimal');
            $table->string('idZona');
            $table->timestamp('fechaEncontrado');
            $table->string('celularPersona')->nullable();
            $table->string('telefonoPersona')->nullable();
            $table->string('emailPersona')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animales_encontrados');
    }
}
