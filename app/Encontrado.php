<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encontrado extends Model
{
    protected $table = "animales_encontrados";

    public $timestamps = false;

    protected $fillable = ['id', 'idUsuario', 'idAnimal', 'idZona', 'fechaEncontrado', 'celularPersona',
        'telefonoPersona', 'emailPersona'];

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
