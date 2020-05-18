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

            $queryFinal = $this->filtrarAnimales($parametros, $query);

            $query->orderBy($parametros["order"], $parametros["direction"])->limit(8);
            
            return $queryFinal->get();
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
        $resultado = $this->crearMascota($perdido['animal'], $imagenPrincipal, $imagenSecundaria);
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
        if (!isset($perdido['animal']) || !isset($perdido['fecha']) || !isset($perdido['celularDuenio'])) {
            $errores[] = 'Faltan campos requeridos, debe enviar: animal, fecha y celularDuenio';
        }
        if (isset($perdido['fecha'])) {
            $fechaPerdido = $perdido['fecha'] !== null && $perdido['fecha'] !== "" ? new Carbon($perdido['fecha']) : null;
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

    public function updatePerdido($bodyContent, $id) {
        DB::beginTransaction();        
        $perdido = Perdido::find($id);
        if (empty($perdido))
            return response()->json([
                'message' => 'Ha ocurrido un error al actualizar el animal perdido',
                'errores' => 'Perdido no encontrado'
            ], 500);
        $bodyPerdido = json_decode($requestPerdido['perdido'], true);
        \Log::info($bodyPerdido);
        $errores = $this->validarCrearPerdido($bodyPerdido);
        if (count($errores) > 0) {
            return response()->json([
                'message' => 'Ha ocurrido un error al actualizar el animal perdido',
                'errores' => $errores
            ], 500);
        } else {
            $perdido->fill($bodyPerdido);
            $imagenPrincipal = null;
            $imagenSecundaria = null;
            if (isset($bodyContent['imagenPrincipal'])) {
                $imagenPrincipal = $bodyContent->file('imagenPrincipal');
            }
            if (isset($bodyContent['imagenSecundaria'])) {
                $imagenSecundaria = $bodyContent->file('imagenSecundaria');
            }
            $resultado = $this->actualizarAnimal($bodyContent['animal'], $imagenPrincipal, $imagenSecundaria);    
            if (isset($resultado['errores'])) {
                DB::rollback();
                return response()->json([
                    'message' => 'Ha ocurrido un error al actualizar la mascota perdida',
                    'errores' => $resultado['errores']
                ], 500);
            }
            try {
                $idLogueado = Auth::user()['id'];
                $perdido->ultUsuarioMdf = $idLogueado;
                $perdido->ultHoraMdf = Carbon::now()->format('Y-m-d');
                $perdido->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'message' => 'Ha ocurrido un error al actualizar el animal perdido',
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
    }
}