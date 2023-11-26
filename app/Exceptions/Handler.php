<?php

namespace App\Exceptions;

use Throwable;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ModelNotFoundException::class,
        AuthenticationException::class,
        AuthorizationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e) {
            if (request()->expectsJson()) {

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'errors' => [],
                    'data' => []
                ], 401);
            }
        });


        $this->renderable(function (ValidationException $e) {
            if (request()->expectsJson()) {

                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $e->errors(),
                    'data' => []
                ], 422);
            }
        });


        $this->renderable(function (Exception $e) {
            if (request()->expectsJson()) {

                if (method_exists($e, 'getResponse')) {
                    return ($e->getResponse());
                } elseif (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                } elseif (method_exists($e, 'getCode')) {
                    $statusCode = $e->getCode();
                } else {
                    $statusCode = 500;
                }

                if (!config('app.debug')) {

                    // dd($statusCode);
                    switch ($statusCode) {

                        case 400:
                            $message = 'The request was invalid.';
                            break;

                        case 401:
                            $message = 'Unauthorized';
                            break;

                        case 403:
                            $message = 'No permission for requested resource';
                            break;

                        case 404:
                            $message = 'The requested resource was not found';
                            break;

                        case 405:
                            $message = 'No permission for requested resource';
                            break;

                        case 408:
                            $message = 'Request Timeout';
                            break;

                        case 429:
                            $message = 'Too many requests';
                            break;

                        case 500:
                            $message = 'Internal Server Error';
                            break;

                        case 503:
                            $message = 'Service Unavailable';
                            break;

                        default:
                            $message = 'Request could not be completed';
                            $statusCode = 500;
                            break;
                    }

                    return response()->json([
                        'status' => false,
                        'message' => $message,
                        'errors' => [],
                        'data' => []
                    ], $statusCode);
                }
            }
        });
    }
}
