<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        DB::rollBack();

        if(method_exists($exception, 'render')) {
            return $exception->render();
        }

        \Log::info("handler");
        \Log::info($exception);
        \Log::info($exception->getMessage());

        switch ($exception->getMessage()) {
            case "CAMBIOAPROBADOAPENDIENTE":
                return Response::json(array(
                    'code' => 500,
                    'message' => "Para pasar a pendiente el pedido debe estar aprobado y no estar facturado."
                ), 500);
                break;
            case "CAMBIOAPROBADOACANCELADO":
                return Response::json(array(
                    'code' => 500,
                    'message' => "Para pasar a cancelado el pedido debe estar aprobado."
                ), 500);
                break;
            case "ESTADOPEDIDOREPETIDO":
                return Response::json(array(
                    'code' => 500,
                    'message' => "El pedido ya está en el estado seleccionado."
                ), 500);
                break;
            default:
                return Response::json(array(
                    'code' => 500,
                    'message' => "Ocurrió un error inesperado. Intentelo nuevamente. Si el problema persiste comuníquese con el administrador."
                ), 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
