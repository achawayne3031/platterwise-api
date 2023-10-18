<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\AppResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response as HttpCode;
use PHPUnit\Framework\MockObject\BadMethodCallException;
use ErrorException;

class Handler extends ExceptionHandler
{
    use AppResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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

        $this->renderable(function (TokenInvalidException $error, $request) {
            return self::clientErrResponse(
                $error->getMessage(),
                HttpCode::HTTP_UNAUTHORIZED,
                $error
            );
        });

        $this->renderable(function (TokenExpiredException $error, $request) {
            return self::clientErrResponse(
                $error->getMessage(),
                HttpCode::HTTP_UNAUTHORIZED,
                $error
            );
        });

        $this->renderable(function (JWTException $error, $request) {
            return self::clientErrResponse($error->getMessage(), 404, $error);
        });

        $this->renderable(function (
            MethodNotAllowedHttpException $error,
            $request
        ) {
            return self::clientErrResponse(
                $error->getMessage(),
                HttpCode::HTTP_METHOD_NOT_ALLOWED,
                $error
            );
        });

        $this->renderable(function (QueryException $error, $request) {
            return self::serverErrResponse($error);
        });

        $this->renderable(function (NotFoundHttpException $error, $request) {
            $response = "The requested route doesn't exists on this resource";
            return self::clientErrResponse(
                $response,
                HttpCode::HTTP_NOT_FOUND,
                $error
            );
        });

        $this->renderable(function (BadMethodCallException $error, $request) {
            return self::serverErrResponse($error);
        });

        $this->renderable(function (ErrorException $error, $request) {
            return self::serverErrResponse($error);
        });
    }
}
