<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    protected $table = "barrios";

    public $timestamps = false;

    protected $fillable = ['id', 'nombre'];
}
