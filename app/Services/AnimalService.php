<?php


namespace App\Services;


use App\Animal;
use Illuminate\Support\Facades\Auth;

trait AnimalService
{
    public function crearMascota($bodyMascota) {
        $idUsuario = Auth::user()['id'];
        $mascota = new Animal($bodyMascota);
        $mascota->idCreador = $idUsuario;
        $mascota->save();
        return $mascota;
    }
}