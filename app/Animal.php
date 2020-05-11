<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = "animales";

    public $timestamps = false;

    protected $fillable = ['id', 'idZona', 'idBarrio', 'nombre', 'sexo', 'tipo', 'edadAproximada', 'castrado', 'tamanio', 'particularidades', 'idCreador', 'ultUsuarioMdf', 'ultHoraMdf'];

    public function creador()
    {
        return $this->belongsTo('App\Usuario', "idCreador", "id");
    }

    public function ultUsuarioMdf()
    {
        return $this->belongsTo('App\Usuario', "ultUsuarioMdf", "id");
    }

    public function particularidades()
    {
        return $this->belongsTo('App\Particularidades', "idParticularidades", "id");
    }

}
