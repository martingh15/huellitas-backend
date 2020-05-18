<?php

namespace App\Http\Controllers;

use App\Mail\ValidarEmail;
use App\Usuario;
use App\Services\UsuarioService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    use UsuarioService;

    public function registro(Request $request)
    {
        return $this->registrarUsuario($request);        
    }

    public function update(Request $request)
    {
        $bodyContent = json_decode($request->getContent(), true);
        return $this->updateUsuario($bodyContent);
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
