<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = "zonas";

    protected $fillable = ['id', 'nombre', 'idUsuario'];

    public function usuario()
    {
        return $this->belongsTo('App\Usuario', "idUsuario", "id");
    }
}
