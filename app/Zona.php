<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = "zonas";

    public $timestamps = false;

    protected $fillable = ['id', 'nombre'];
}
