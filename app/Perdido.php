<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perdido extends Model
{
    protected $table = "animales_perdidos";

    protected $fillable = ['id', 'idUsuario', 'idAnimal', 'idZona', 'fechaPerdido'];

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
        return $this->belongsTo('App\Zona', "idZona", "id");
    }
}
