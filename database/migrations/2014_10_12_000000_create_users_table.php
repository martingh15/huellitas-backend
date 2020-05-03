<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('usuarios', function (Blueprint $table) {
//            $table->increments('id')->unsigned();
//            $table->string('nombre');
//            $table->string('email')->unique();
//            $table->string('tokenEmail',255)->nullable();
//            $table->string('tokenReset',255)->nullable();
//            $table->timestamp('fechaTokenReset')->nullable();
//            $table->boolean('habilitado')->default(false);
//            $table->string('password');
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
//        Schema::dropIfExists('usuarios');
    }
}
