<?php

namespace App\Http\Controllers;

use App\Animal;
use App\Services\AnimalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AnimalController extends Controller
{
    use AnimalService;

    public function store(Request $request) {
        $bodyContent = json_decode($request->getContent(), true);
        return $this->crearMascota($bodyContent);
    }

    public function update(Request $request) {
        $idUsuario = Auth::user()['id'];
        $bodyContent = json_decode($request->getContent(), true);
        if (!isset($bodyContent['id'])) {
            return response()->json([
                'code' => 200,
                'message' => "Hubo un problema al guardar su mascota."
            ], 200);
        }
        $mascota = Animal::where('id', '=', $bodyContent['id'])->with('particularidades')->first();
        $mascota = Animal::find($bodyContent['id']);
        if (!$mascota) {
            return response()->json([
                'code' => 500,
                'message' =>  "Hubo un problema, no hemos encontrado su mascota."
            ], 500);
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
