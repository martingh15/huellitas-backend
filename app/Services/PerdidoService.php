<?php


namespace App\Services;

use App\Perdido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

trait PerdidoService
{
    use AnimalService;

    public function getAll($parametros)
    {
        if (array_key_exists('id', $parametros) && !empty($parametros["id"])) {
            return Perdido::with("Animal")->where("id", $parametros["id"])->get();
        } else {
            $query = Perdido::select('animales_perdidos.*')
                ->join('animales', 'animales_perdidos.idAnimal', 'animales.id')
                ->where('habilitado',1)
                ->with("Animal");
            if (array_key_exists('tipo', $parametros) && !empty($parametros["tipo"])) {
                $query->where("animales.tipo", $parametros["tipo"]);
            }
            if (array_key_exists('castrado', $parametros) 
                && ($parametros['castrado'] === "1" || $parametros['castrado'] === "0")) {
                $query->where("animales.castrado", $parametros["castrado"]);
            }
            
            if (array_key_exists('sexo', $parametros) 
                && ($parametros['sexo'] === "1" || $parametros['sexo'] === "0")) {
                $query->where("animales.sexo", $parametros["sexo"]);
            }

            if (array_key_exists('idZona', $parametros) && !empty($parametros["idZona"])) {
                $query->where("animales.idZona", $parametros["idZona"]);
            }

            if (array_key_exists('tamanio', $parametros) && !empty($parametros["tamanio"])) {
                $query->where("animales.tamanio", 'like', '%' . $parametros["tamanio"] . '%');
            }

            if (array_key_exists('edadMinima', $parametros) && $parametros["edadMinima"] !== null) {
                if (intval($parametros['edadMinima']) > 10) {
                    $query->where(function ($query) use ($parametros) {
                        $query->where("animales.edadAproximada", '!=', ',0,1,2,');
                        $query->where("animales.edadAproximada", '!=', ',2,3,4,5,6,7,8,9,10,');
                        $query->where("animales.edadAproximada", '>=', intval($parametros["edadMinima"]));
                        $query->orWhere("animales.edadAproximada", ',10,*,');
                    });                    
               } else if (intval($parametros["edadMinima"]) > 2 && intval($parametros["edadMinima"]) <= 10) {
                   $query->where(function ($query) use ($parametros) {
                        $query->where("animales.edadAproximada", '!=', ',0,1,2,');
                        $query->where("animales.edadAproximada", '>=', intval($parametros["edadMinima"]));
                        $query->orWhere("animales.edadAproximada", ',10,*,');
                        $query->orWhere("animales.edadAproximada", ',2,3,4,5,6,7,8,9,10,');
                    });                                       
                } else if (intval($parametros["edadMinima"]) <= 2) {
                    $query->where(function ($query) use ($parametros) {
                        $query->where("animales.edadAproximada", '>=', intval($parametros["edadMinima"]));
                        $query->orWhere("animales.edadAproximada", ',10,*,');
                        $query->orWhere("animales.edadAproximada", ',2,3,4,5,6,7,8,9,10,');
                        $query->orWhere("animales.edadAproximada", ',0,1,2,');
                    });  
                }
            }

            if (array_key_exists('edadMaxima', $parametros) && $parametros["edadMaxima"] !== null) {
                if (intval($parametros['edadMaxima']) === 0) {
                    $query->where("animales.edadAproximada", '!=', ',2,3,4,5,6,7,8,9,10,');
                    $query->where("animales.edadAproximada", '!=', ',10,*,');
                    $query->where("animales.edadAproximada", ',0,1,2,');
                } else if (intval($parametros["edadMaxima"]) > 0 && intval($parametros["edadMaxima"]) <= 9) {
                    if (intval($parametros["edadMaxima"]) === 1) {
                        $query->where("animales.edadAproximada", '!=', ',2,3,4,5,6,7,8,9,10,');
                    }                    
                    $query->where("animales.edadAproximada", '!=', ',10,*,');
                    $query->where(function ($query) use ($parametros) {
                        $query->orWhere("animales.edadAproximada", ",0,1,2,");
                        $query->orWhere("animales.edadAproximada", '<=', intval($parametros["edadMaxima"]));
                    });                    
                } else if (intval($parametros["edadMaxima"]) >= 10) {
                    $query->where(function ($query) use ($parametros) {
                        $query->orWhere("animales.edadAproximada", ',0,1,2,');
                        $query->orWhere("animales.edadAproximada", ",2,3,4,5,6,7,8,9,10,");
                        $query->orWhere("animales.edadAproximada", ',10,*,');
                        $query->orWhere("animales.edadAproximada", '<=', intval($parametros["edadMaxima"]));                     
                    });
                }
            }

            if (array_key_exists('registros', $parametros) && $parametros["registros"] > 0) {
                $query->offset($parametros["registros"]);
            }
            
            $query->orderBy($parametros["order"], $parametros["direction"])->limit(8);
            
            return $query->get();
        }
    }

    /**
     * Crea un nueva mascota perdida
     *
     * @param Request $requestPerdido
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearNuevoPerdido(Request $requestPerdido)
    {
        DB::beginTransaction();
        $perdido = json_decode($requestPerdido['perdido'], true);
        $errores = $this->validarCrearPerdido($perdido);
        if (count($errores) > 0) {
            return response()->json([
                'message' => 'Ha ocurrido un error al guardar el animal perdido',
                'errores' => $errores
            ], 500);
        }

        $imagenPrincipal = $requestPerdido->file('imagenPrincipal');
        $imagenSecundaria = null;
        if (isset($requestPerdido['imagenSecundaria'])) {
            $imagenSecundaria = $requestPerdido->file('imagenSecundaria');
        }
        $resultado = $this->crearMascota($perdido['mascota'], $imagenPrincipal, $imagenSecundaria);
        if (isset($resultado['errores'])) {
            DB::rollback();
            return response()->json([
                'message' => 'Ha ocurrido un error al registrar la mascota',
                'errores' => $resultado['errores']
            ], 500);
        }
        try {
            $idLogueado = Auth::user()['id'];
            $perdido = new Perdido($perdido);
            $perdido->idAnimal = $resultado->id;
            $perdido->idCreador = $idLogueado;
            $perdido->ultUsuarioMdf = $idLogueado;
            $perdido->ultHoraMdf = Carbon::now()->format('Y-m-d');
            $perdido->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Ha ocurrido un error al guardar el animal perdido',
                'errores' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => [
                'message' => 'Se ha creado el animal perdido con éxito',
                'perdido' => $perdido
            ]
        ], 200);
    }

    /**
     * Valida que los datos de mascota perdida sean correctos
     *
     * @param $perdidoBody
     * @return array
     */
    protected function validarCrearPerdido($perdido)
    {
        $errores = [];
        if (!isset($perdido['mascota']) || !isset($perdido['fechaPerdido']) || !isset($perdido['celularDuenio'])) {
            $errores[] = 'Faltan campos requeridos, debe enviar: mascota, fechaPerdido y celularDuenio';
        }
        if (isset($perdido['fechaPerdido'])) {
            $fechaPerdido = $perdido['fechaPerdido'] !== null && $perdido['fechaPerdido'] !== "" ? new Carbon($perdido['fechaPerdido']) : null;
            if ($fechaPerdido && Carbon::createFromFormat('Y-m-d H:i:s', $fechaPerdido) === false) {
                $errores[] = "La fecha desde no respeta el formato YYYY-MM-DD";
            } else if ($fechaPerdido === null) {
                $errores[] = "La fecha no puede ser null o string vacío";
            }
            if ($fechaPerdido && $fechaPerdido->isAfter(Carbon::now()->endOfDay())) {
                $errores[] = "La fecha de perdido no puede ser posterior a la de hoy";
            }
        }
        return $errores;
    }
}