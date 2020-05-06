<?php


namespace App\Services;

use App\Perdido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

trait PerdidoService
{
    use AnimalService;

    public function getAll($parametros)
    {
        if (array_key_exists('id', $parametros) && !empty($parametros["id"])) {
            return Perdido::with("Animal")->where("id", $parametros["id"])->get();
        } else {
            $query = Perdido::from('animales_perdidos as p')->select("p.*")->with("Animal");
            if (array_key_exists('searchPhase', $parametros) && !empty($parametros["searchPhase"])) {
                $query->where(function ($query) use ($parametros) {
//                    $query->orWhere("vd_idPedido", 'like', '%' . $parametros["searchPhase"] . '%');
//                    $query->orWhere("fechaPedido", '=', $parametros["searchPhase"]);
//                    $query->orWhere("direccionEntrega", 'like', '%' . $parametros["searchPhase"] . '%');
//                    $query->orWhere("clientes.razonSocial", 'like', '%' . $parametros["searchPhase"] . '%');
//                    $query->orWhere("ep.desEstadoPedido", 'like', '%' . $parametros["searchPhase"] . '%');
                });
            }

            if (array_key_exists('registros', $parametros) && $parametros["registros"] > 0) {
                $query->offset($parametros["registros"]);
            }

            $query->limit(30)->orderBy($parametros["order"], $parametros["direction"])->orderBy('fechaPerdido', 'DESC');

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
        $perdido = json_decode($requestPerdido['perdido'], true);
        $errores = $this->validarCrearPerdido($perdido);
        if (count($errores) > 0) {
            return response()->json([
                'message' => 'Ha ocurrido un error al guardar el animal perdido',
                'errores' => $errores
            ], 500);
        }

//        $imagenPrincipal = $requestPerdido->file('imagenPrincipal');
//        $imagenSecundaria = null;
//        if (isset($requestPerdido['imagenSecundaria'])) {
//            $imagenSecundaria = $requestPerdido->file('imagenSecundaria');
//        }
        $resultado = $this->crearMascota($perdido['mascota'], null, null);
        if (isset($resultado['errores'])) {
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
        } catch (Exception $e) {
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