<?php

namespace App\Http\Controllers;

use Auth;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UsuarioController extends Controller
{
    public function registro(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required',
            'password'   => 'required|confirmed',
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
        $usuario->save();

        return Response::json(array(
            'code' => 200,
            'message' => "Usuario creado correctamente."
        ), 200);
    }

    public function index() {
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
