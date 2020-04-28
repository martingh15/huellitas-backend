<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAnimalParticularidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animales_particularidades', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->bigInteger('idUsuario');
            $table->bigInteger('idAnimal');
            $table->boolean('sordo')->default(false);
            $table->boolean('ciego')->default(false);
            $table->boolean('tresPatas')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animales_particularidades');
    }
}
