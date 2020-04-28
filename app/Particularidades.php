<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Particularidades extends Model
{
    protected $table = "animales_particularidades";

    protected $fillable = ['id', 'idUsuario', 'idAnimal', 'sordo', 'ciego', 'tresPatas'];

    public function usuario()
    {
        return $this->belongsTo('App\Usuario', "idUsuario", "id");
    }

    public function animal()
    {
        return $this->belongsTo('App\Animal', "idAnimal", "id");
    }
}
