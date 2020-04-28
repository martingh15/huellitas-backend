<?php

namespace App\Http\Controllers;

use App\Mail\ValidarEmail;
use App\Usuario;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function registro(Request $request)
    {
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

        $usuario = new Usuario();
        $usuario->email = $request['email'];
        $usuario->password = Hash::make($request['password']);
        $usuario->nombre = $request['nombre'];
        $usuario->tokenEmail = Str::random(64);
        $usuario->save();

        Mail::to($usuario->email)->send(new ValidarEmail($usuario));

        return Response::json(array(
            'code' => 200,
            'message' => "Usuario creado correctamente."
        ), 200);
    }

    public function update(Request $request)
    {
        $bodyContent = json_decode($request->getContent(), true);
        $usuario = Usuario::find($bodyContent["id"]);
        \Log::info('antes del fill: ' . $usuario);
        $usuario->fill($bodyContent);
        \Log::info('despues del fill: ' . $usuario);
        if (empty($usuario))
            return Response::json(array(
                'code' => 401,
                'message' => "Usuario no encontrado, ingresen nuevamente"
            ), 401);
        if (isset($bodyContent['nombre_modificado']) && $bodyContent['nombre_modificado'] !== "") {
            $usuario->nombre = $bodyContent['nombre_modificado'];
            \Log::info('despues del modificado: ' . $usuario);
        }
        $usuario->password = Hash::make($request['password']);
        $usuario->tokenReset = null;
        $usuario->fechaTokenReset = null;
        $usuario->save();
        return response(['usuario' => $usuario]);

    }

    public function index()
    {
        return Usuario::all();
    }

    public function store(Request $request)
    {

    }

    public function create()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}
