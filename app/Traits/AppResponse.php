<?php

namespace App\Traits;

trait AppResponse
{
    public static function informationResponse(
        string $response,
        int $code = 102
    ) {
        return response()->json(
            [
                'status' => true,
                'status_code' => $code,
                'response' => $response,
                'data' => null,
            ],
            $code
        );
    }

    public static function successResponse(
        string $response,
        int $code = 200,
        $data = null
    ) {
        return response()->json([
            'status' => true,
            'status_code' => $code,
            'response' => $response,
            'data' => $data,
        ]);
    }

    public static function clientErrResponse(
        $response,
        int $code = 400,
        $error = null
    ) {
        return response()->json(
            [
                'status' => false,
                'status_code' => $code,
                'response' => $response,
                'data' => null,
                'error_data' =>
                    'Error in this file: ' .
                    $error->getFile() .
                    ' on line ' .
                    $error->getLine(),
            ],
            $code
        );
    }

    public static function serverErrResponse(object $error, int $code = 500)
    {
        return response()->json(
            [
                'status' => false,
                'status_code' => $code,
                'response' =>
                    'Oops, something went wrong. Please try again later',
                'data' => null,
                'debug' => env('APP_DEBUG') ? $error->getMessage() : null,
                'error_data' =>
                    'Error in this file: ' .
                    $error->getFile() .
                    ' on line ' .
                    $error->getLine(),
            ],
            $code
        );
    }

    public static function generateToken(
        string $response,
        object $data,
        int $code = 200
    ) {
        return response()->json(
            [
                'status' => true,
                'status_code' => $code,
                'response' => $response,
                'data' => [
                    'authorization' => [
                        'type' => 'Bearer',
                        'token' => $data->token,
                        'expires_in' =>
                            auth()
                                ->factory()
                                ->getTTL() * 60,
                    ],
                    'data' => $data->data,
                ],
            ],
            $code
        );
    }
}
