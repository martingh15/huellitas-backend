<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perdido extends Model
{
    protected $table = "animales_perdidos";

    public $timestamps = false;

    protected $fillable = ['id', 'idAnimal', 'fecha', 'celularDuenio', 'celularSecundario', 'idCreador', 'ultUsuarioMdf', 'ultHoraMdf'];

    public function usuario()
    {
        return $this->belongsTo('App\Usuario', "idUsuario", "id");
    }

    public function animal()
    {
        return $this->belongsTo('App\Animal', "idAnimal", "id");
    }

    public function zona()
    {
        return $this->belongsTo('App\Barrio', "idZona", "id");
    }
}
