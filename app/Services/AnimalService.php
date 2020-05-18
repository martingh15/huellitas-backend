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
        $errores = $this->validarMascota($bodyMascota);
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
            $mascota->idCreador = $idLogueado;
            $mascota->ultUsuarioMdf = $idLogueado;
            $mascota->ultHoraMdf = Carbon::now()->format('Y-m-d');
            $mascota->save();
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
        $nombre = $esPrincipal ? 'principal' : 'secundaria';
        $fileName = "$mascota->id-" . $nombre . "-" . uniqid() . "." . $imagen->getClientOriginalExtension();
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
            $mascota->fileImagenPrincipal = $imagen->getClientOriginalName();
        } else {
            $mascota->imagenSecundaria = $fileName;
            $mascota->fileImagenSecundaria = $imagen->getClientOriginalName();
        }
        $mascota->save();
        $img->save($carpeta);
        return [
            'success' => 'Se agrego la imagen con éxito'
        ];
    }

    public function actualizarAnimal($bodyMascota, $imagen1, $imagen2) {
        $errores = $this->validarMascota($bodyMascota);
        if (count($errores) > 0) {
            return [
                'errores' => $errores
            ];
        }
        try {
            $mascota = Animal::find($bodyMascota["id"]);
            $mascota->fill($bodyMascota);
            $mascota->save();
             if ($imagen1 !== null) {
                $resultado1 = $this->crearImagenesAnimal($mascota, $imagen1, false);
                if (isset($resultado1['error'])) {
                    return [
                        'errores' => $resultado1['error']
                    ];
                }
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
        
        return $mascota;
    }

    /**
     * Valida que los datos de la mascota sean correctos
     *
     * @param $mascota
     * @return array
     */
    protected function validarMascota($mascota) {
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

        if(!strrpos($$mascota['tamanio'], "1") && !strrpos($$mascota['tamanio'], "2") && !strrpos($$mascota['tamanio'], "3")) {
            $errores[] = 'El campo tamanio debe ser 1, 2 o 3 para "Chico", "Mediano", "Grande" respectivamente';
        }

        if (intval($mascota['edadAproximada']) !== 1 && intval($mascota['edadAproximada']) !== 2 && intval($mascota['edadAproximada']) !== 3) {
            $errores[] = 'El campo edadAproximada debe ser 1, 2 o 3 para los 3 rangos';
        }
        return $errores;
    }

    public function filtrarAnimales($parametros, $query) {
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

        if (array_key_exists('edadAproximada', $parametros) && $parametros["edadAproximada"] !== null) {
            $query->where("animales.edadAproximada", $parametros["edadAproximada"]);
        }

        if (array_key_exists('registros', $parametros) && $parametros["registros"] > 0) {
            $query->offset($parametros["registros"]);
        }
    
        return $query;
    }
}