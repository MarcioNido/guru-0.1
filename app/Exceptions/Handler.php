<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
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
     * @return \Illuminate\Http\Response|JsonResponse|Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return $this->jsonExceptionResponse($exception);
        }
        return parent::render($request, $exception);
    }

    /**
     * Generate a JSON exception response
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonExceptionResponse(Exception $exception)
    {

        if ($exception instanceof HttpException) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], $exception->getStatusCode());
        }

        return response()->json(['message' => 'Error ' . $exception->getMessage()], 404);

    }
}
