<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = "animales";

    protected $fillable = ['id', 'idUsuario', 'idParticularidades', 'nombre', 'sexo', 'edadAproximada', 'castrado', 'tamanio',
        'celularDuenio', 'telefonoDuenio', 'emailDuenio'];

    public function usuario()
    {
        return $this->belongsTo('App\Usuario', "idUsuario", "id");
    }

    public function particularidades()
    {
        return $this->belongsTo('App\Particularidades', "idParticularidades", "id");
    }

}
