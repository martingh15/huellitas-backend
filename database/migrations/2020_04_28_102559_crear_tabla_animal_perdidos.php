<?php

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
            $table->bigInteger('idUsuario');
            $table->bigInteger('idAnimal');
            $table->string('idZona');
            $table->timestamp('fechaPerdido');
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
