<?php

namespace App\Http\Controllers;

use App\Mail\OlvidePassword;
use Illuminate\Http\Request;
use App\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JWTAuth;
use Illuminate\Support\Facades\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        // credenciales para loguear al usuario
        $credentials = $request->only('email', 'password');

        try {
            //Busco por mail
            $user = Usuario::where("email", $credentials['email'])->first();
            if (empty($user)) {
                return Response::json(array(
                    'code' => 500,
                    'message' => "Usuario y/o contraseña incorrectos."
                ), 500);
            }
            if ($user->habilitado == 0) {
                return Response::json(array(
                    'code' => 500,
                    'message' => "Su usuario no ha sido habilitado aún. Aguarde la habilitación o contáctese con nosotros."
                ), 500);
            }
            if (!Hash::check($credentials['password'], $user->password)) {
                return Response::json(array(
                    'code' => 500,
                    'message' => "Usuario y/o contraseña incorrectos."
                ), 500);
            }
            //Genero token
            $token = JWTAuth::fromUser($user, ['idUsuario' => $user->id, 'nombre' => $user->nombre . " " . $user->apellido]);
            //Si hubo problema con token
            if (!$token) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // si no se puede crear el token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'))->header('Access-Control-Allow-Origin', '*');
    }

    public function olvidoPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required'
        ]);
        $credentials = $request->only('email');
        $user = Usuario::where("email", $credentials['email'])->first();

        if (empty($user))
            return Response::json(array(
                'code' => 500,
                'message' => "El email ingresado no corresponde a ningún usuario registrado."
            ), 500);

        //Genero token
        $tokenReset = Str::random(64);
        $fechaExpiraToken = (new \DateTime())->setTimezone(new \DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaExpiraToken->add(new \DateInterval('PT' . 1440 . 'M'));

        //Guardo token y fecha expira
        $user->tokenReset = $tokenReset;
        $user->fechaTokenReset = $fechaExpiraToken;
        $user->save();

        Mail::to($user->email)->send(new OlvidePassword($user));
        return Response::json(array(
            'code' => 200,
            'message' => "Se ha enviado un link a su email para reiniciar su contraseña. Tiene 24 horas para cambiarla."
        ), 200);
    }

    /**
     * Resetea a contraseña de un usuario alidad que el token sea válido
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'tokenReset' => 'required',
            'password'   => 'required|confirmed'
        ]);
        $credentials = $request->only('email');

        $fechaHoy = (new \DateTime())->setTimezone(new \DateTimeZone("America/Argentina/Buenos_Aires"));
        $user = Usuario::where([["tokenReset", $request['tokenReset']], ["fechaTokenReset", ">=", $fechaHoy]])->first();
        if (empty($user))
            return Response::json(array(
                'code' => 500,
                'message' => "El token ingresado no es válido o ha caducado. Recuerda que tiene 24 " .
                    "horas para cambiar la contraseña"
            ), 500);
        $user->password = Hash::make($request['password']);
//        $user->tokenReset = null;
//        $user->fechaTokenReset = null;
        $user->save();
        return response(['usuario' => $user]);
    }

    /**
     * Valida que el token sea válido
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function validarToken(Request $request)
    {
        $this->validate($request, [
            'tokenReset' => 'required'
        ]);
        $validatedData = $request->only('tokenReset');

        //Busco usuario que coincida con el token y fecha
        $fechaHoy = (new \DateTime())->setTimezone(new \DateTimeZone("America/Argentina/Buenos_Aires"));
        $user = Usuario::where([["tokenReset", $validatedData['tokenReset']], ["fechaTokenReset", ">=", $fechaHoy]])->first();
        if (empty($user))
            return Response::json(array(
                'code' => 500,
                'message' => "El token ingresado no es válido o ha caducado. Vuelva a solicitar el cambio de contraseña."
            ), 500);
        else {
            return Response::json(array(
                'code' => 200,
                'message' => "El token fue validado correctamente."
            ), 200);
        }
    }

    /**
     * Si el usuario intenta acceder a una ruta protegida por autenticación le indica
     * que no puede acceder a la misma
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirect()
    {
        return Response::json(array(
            'code' => 500,
            'message' => "No esta autorizado a ingresar a esta ruta."
        ), 500);
    }
}
