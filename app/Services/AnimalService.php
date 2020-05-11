<?php


namespace App\Services;


use App\Animal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

trait AnimalService
{

    /**
     * Crea una nueva mascota
     *
     * @param $bodyMascota
     * @return Animal|array
     */
    public function crearMascota($bodyMascota, $imagen1, $imagen2) {
        $errores = $this->validarCrearMascota($bodyMascota);
        if (count($errores) > 0) {
            return [
                'errores' => $errores
            ];
        }
        try {
            $idLogueado = Auth::user()['id'];
            $mascota = new Animal($bodyMascota);
            if ($bodyMascota['castrado'] === 1 || $bodyMascota['castrado'] === "1") {
                $mascota->castrado = true;
            } else if ($bodyMascota['castrado'] === 2 || $bodyMascota['castrado'] === "2") {
                $mascota->castrado = false;
            }
            if (isset($bodyMascota['rangoEdad']) &&  $bodyMascota['rangoEdad'] === 1) {
                switch($bodyMascota['edadAproximada']) {
                    case 1:
                        $mascota->edadAproximada = ',0,1,2,';
                        break;
                    case 2:
                        $mascota->edadAproximada = ',2,3,4,5,6,7,8,9,10,';
                        break;
                    case 3:
                        $mascota->edadAproximada = ',10,*,';
                        break;
                    default:
                        $mascota->edadAproximada = '';
                        break;
                }
            } else {
                if (intval($bodyMascota['edadAproximada']) !== 0) {
                    $mascota->edadAproximada = $bodyMascota['edadAproximada']; 
                } else {
                    $mascota->edadAproximada = ',0,1,2,';
                }
                
            }
            $mascota->tamanio = $this->getTextoTamanio($bodyMascota['tamanio']);
            $mascota->idCreador = $idLogueado;
            $mascota->ultUsuarioMdf = $idLogueado;
            $mascota->ultHoraMdf = Carbon::now()->format('Y-m-d');
            $mascota->save();
            \Log::info('Mascota creada: ' . $mascota);
            $resultado1 = $this->crearImagenesAnimal($mascota, $imagen1, true);
            if (isset($resultado1['error'])) {
                return [
                    'errores' => $resultado1['error']
                ];
            }
            if ($imagen2 !== null) {
                $resultado2 = $this->crearImagenesAnimal($mascota, $imagen2, false);
                if (isset($resultado2['error'])) {
                    return [
                        'errores' => $resultado2['error']
                    ];
                }
            }
            return $mascota;
        } catch(Exception $e) {
            return [
                'errores' => $e->getMessage()
            ];
        }
    }

    protected function crearImagenesAnimal($mascota, $imagen, $esPrincipal) {
        //custom mensajes en las validaciones.
        $messages = [
            'image.mimes' => 'La imagen debe ser .png, .jpg, .jpeg o .gif',
            'image.max' => "La imagen debe teber un tamaño menor a 2MB",
            'image.uploaded' => "Ocurrió un error al intentar subir la imagen"
        ];

        //creamos un array con la imagen para validad.
        $imgArray = array('image' => $imagen);

        //ponemos reglas de validación
        $rules = array(
            'image' => 'mimes:jpeg,jpg,png,gif|max:2000'
        );

        //llamamos al validator con la imagen las reglas y los custom mensajes
        $validator = Validator::make($imgArray, $rules, $messages);

        //Chequeamos las validaciones.
        if ($validator->fails()) {
            return [
                'error' => $validator->errors()->getMessages()
            ];
        }

        //nombre de la imagen con idUnico-idGremio, obtengo la extension original del archivo
        $id = $esPrincipal ? 1 : 2;
        $fileName = "$mascota->id-" . $id . "-" . uniqid() . "." . $imagen->getClientOriginalExtension();
        $carpeta = public_path() . '/img/animales/' . $fileName;
        $img = Image::make($imagen);

        //Altura de la imagen a redimensionar en px
        $height = 600;
        //Redimensiono la imagen manteniedno aspectRatio
        $img->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        //Guardo la imagen
        if ($esPrincipal) {
            $mascota->imagenPrincipal = $fileName;
        } else {
            $mascota->imagenSecundaria = $fileName;
        }
        $mascota->save();
        $img->save($carpeta);
        return [
            'success' => 'Se agrego la imagen con éxito'
        ];
    }

    /**
     * Valida que los datos de la mascota sean correctos
     *
     * @param $mascota
     * @return array
     */
    protected function validarCrearMascota($mascota) {
        $errores = [];
        if (!isset($mascota['sexo']) || !isset($mascota['edadAproximada']) || !isset($mascota['rangoEdad'])
            || !isset($mascota['castrado']) || !isset($mascota['tamanio']) || !isset($mascota['imagenPrincipal'])
        ) {
            $errores[] = ['Faltan campos de mascota, debe enviar: imagenPrincipal, sexo, edadAproximada, rangoEdad, castrado y tamanio'];
            return $errores;
        }

        if($mascota['sexo'] !== 0 && $mascota['sexo'] !== 1) {
            $errores[] = 'El campo sexo debe ser 1 para Macho o 0 para Hembra';
        }

        if($mascota['castrado'] !== 0 && $mascota['castrado'] !== 1  && $mascota['castrado'] !== 2 ) {
            $errores[] = 'El campo castrado debe 0, 1, 2 para "No", "Si", "No lo sé" respectivamente';
        }

        if(is_array($mascota['tamanio'])
            && !in_array("1", $mascota['tamanio'])
            && !in_array("2", $mascota['tamanio'])
            && !in_array("3", $mascota['tamanio'])
            && !in_array(1, $mascota['tamanio'])
            && !in_array(2, $mascota['tamanio'])
            && !in_array(3, $mascota['tamanio'])
        ) {
            $errores[] = 'El campo tamanio debe ser 1, 2 o 3 para "Chico", "Mediano", "Grande" respectivamente';
        }

        if ($mascota['rangoEdad'] ===  false && intval($mascota['edadAproximada']) < 0) {
            $errores[] = 'El campo edadAproximada no puede ser menor a cero';
        }
        return $errores;
    }

    /**
     * Devuelve el nombre del tamaño de un animal
     *
     * @param $tamanio
     * @return string
     */
    protected function getTextoTamanio($tamanio) {
        $string = "";
        foreach ($tamanio as $key => $value) {
            $string .= $value . ',';        
        }
        return $string;
    }
}