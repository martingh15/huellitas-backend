<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Particularidades extends Model
{
    protected $table = "animales_particularidades";

    protected $fillable = ['id', 'idCreador', 'ultUsuarioMdf', 'ultHraModifico', 'idAnimal', 'sordo', 'ciego', 'tresPatas'];

    public $timestamps = false;

    public function creador()
    {
        return $this->belongsTo('App\Usuario', "idCreador", "id");
    }

    public function ultUsuarioMdf()
    {
        return $this->belongsTo('App\Usuario', "ultUsuarioMdf", "id");
    }

    public function animal()
    {
        return $this->belongsTo('App\Animal', "idAnimal", "id");
    }
}
