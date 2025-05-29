<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Una lista de las excepciones que no se deben reportar.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Una lista de las entradas que nunca se deben mostrar en las excepciones de validación.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra los callbacks para el manejo de excepciones.
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Personaliza la respuesta para peticiones AJAX (por ejemplo, subida de archivos).
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Operación completada con éxito.'
            ], 200);
        }
        return parent::render($request, $exception);
    }
}
