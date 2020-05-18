<?php


namespace App\Services;

use App\Usuario;
use Illuminate\Http\Request;
use App\Mail\ValidarEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

trait UsuarioService
{

    public function registrarUsuario(Request $request) {
        $this->validate($request, [
            'email' => 'email|required',
            'password' => 'required|confirmed',
            'nombre' => 'required'
        ]);

        $usuarioGuardado = Usuario::where('email', $request['email'])->first();

        if (!empty($usuarioGuardado)) {
            return Response::json(array(
                'code' => 500,
                'message' => "Ya existe un usuario con ese email."
            ), 500);
        }

        try {
            DB::beginTransaction();
            $usuario = new Usuario();
            $usuario->email = $request['email'];
            $usuario->password = Hash::make($request['password']);
            $usuario->nombre = $request['nombre'];
            $usuario->tokenEmail = Str::random(64);
            $usuario->save();

            Mail::to($usuario->email)->send(new ValidarEmail($usuario));
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Ha ocurrido un error al crear el usuario',
                'errores' => $e->getMessage()
            ], 500);
        }
        return Response::json(array(
            'code' => 200,
            'message' => "Usuario creado correctamente."
        ), 200);
    }

    public function updateUsuario($bodyContent) {
        $usuario = Usuario::find($bodyContent["id"]);
        $usuario->fill($bodyContent);
        if (empty($usuario))
            return Response::json(array(
                'code' => 401,
                'message' => "Usuario no encontrado, ingresen nuevamente"
            ), 401);
        if (isset($bodyContent['nombre_modificado']) && $bodyContent['nombre_modificado'] !== "") {
            $usuario->nombre = $bodyContent['nombre_modificado'];
        }
        if (isset($bodyContent['password'])) {
            $usuario->password = Hash::make($bodyContent['password']);
        }        
        $usuario->tokenReset = null;
        $usuario->fechaTokenReset = null;
        $usuario->save();
        return response(['usuario' => $usuario]);
    }
}