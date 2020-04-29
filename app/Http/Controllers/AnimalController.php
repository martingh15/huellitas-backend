<?php

namespace App\Http\Controllers;

use App\Animal;
use App\Particularidades;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AnimalController extends Controller
{
    public function store(Request $request) {
        $idUsuario = Auth::user()['id'];
        $bodyContent = json_decode($request->getContent(), true);
        $mascota = new Animal($bodyContent);
        $mascota->idCreador = $idUsuario;
        $mascota->save();

        if (isset($bodyContent['particularidades'])) {
            $particularidades = new Particularidades($bodyContent['particularidades']);
            $particularidades->idAnimal = $mascota->id;
            $particularidades->idCreador = $idUsuario;
            $particularidades->save();
            $mascota->idParticularidades = $particularidades->id;
        }
        $mascota->save();
        return $mascota;
    }

    public function update(Request $request) {
        $idUsuario = Auth::user()['id'];
        $bodyContent = json_decode($request->getContent(), true);
        if (!isset($bodyContent['id'])) {
            return Response::json(array(
                'code' => 200,
                'message' => "Hubo un problema al guardar su mascota."
            ), 200);
        }
        $mascota = Animal::where('id', '=', $bodyContent['id'])->with('particularidades')->first();
        \Log::info($mascota);
        $mascota = Animal::find($bodyContent['id']);
        \Log::info($mascota);
        if (!$mascota) {
            return Response::json(array(
                'code' => 200,
                'message' => "Hubo un problema, no hemos encontrado su mascota."
            ), 200);
        }
        $mascota->fill($bodyContent);
        $mascota->ultUsuarioMdf = $idUsuario;
        $mascota->ultHraModifico = Carbon::now();
        $mascota->save();
        if (isset($bodyContent['particularidades'])) {
            $particularidades = $mascota->particularidades;
            $particularidades->idAnimal = $mascota->id;
            $particularidades->ultUsuarioMdf = $idUsuario;
            $particularidades->ultHraModifico =  Carbon::now();;
            $particularidades->save();
            $mascota->particularidades = $particularidades;
        }
        return $mascota;
    }
}
