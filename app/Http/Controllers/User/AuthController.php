<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Validations\UserAuthValidator;
use App\Validations\ErrorValidation;

use App\Helpers\ResponseHelper;
use App\Models\User\AppUser;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserAuthValidator::validate_rules($request, 'register');

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $verify_token = Func::generate_reference(10, 'all');

                    $data = [
                        'full_name' => $request->full_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'location' => $request->location,
                        'username' => $request->username,
                        'password' => bcrypt($request->password),
                        'verify_token' => $verify_token,
                    ];

                    $register = DBHelpers::create_query(AppUser::class, $data);

                    if ($register) {
                        $data = [
                            'link' =>
                                'https://api2.platterwise.com/verify-user/' .
                                $register->id .
                                '/' .
                                $verify_token,
                            'user' => $register->id,
                            'verify_token' => $verify_token,
                        ];

                        \Mail::to($request->email)->send(
                            new \App\Mail\UserEmailVerification($data)
                        );

                        return ResponseHelper::success_response(
                            'Registration was successful, verification link sent to your email',
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'Registration failed, Database insertion issues',
                            $validate->errors(),
                            401
                        );
                    }
                } catch (Exception $e) {
                    return ResponseHelper::error_response(
                        'Server Error',
                        $e->getMessage(),
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = [
                    'full_name',
                    'phone',
                    'email',
                    'password',
                    'username',
                ];
                $error_res = ErrorValidation::arrange_error($errors, $props);

                return ResponseHelper::error_response(
                    'validation error',
                    $error_res,
                    401
                );
            }
        } else {
            return ResponseHelper::error_response(
                'HTTP Request not allowed',
                '',
                404
            );
        }
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserAuthValidator::validate_rules($request, 'login');

            $verify_token = Func::generate_reference(10, 'all');

            if (!$validate->fails() && $validate->validated()) {
                if (
                    $token = Auth::guard('api')->attempt([
                        'email' => $request->email,
                        'password' => $request->password,
                    ])
                ) {
                    $token = $this->respondWithToken($token);
                    $user = $this->me();
                    $check_user = auth()->user();

                    if ($check_user->is_verified == 0) {
                        $data = [
                            'link' =>
                                'https://api2.platterwise.com/verify-user/' .
                                $check_user->id .
                                '/' .
                                $verify_token,
                            'user' => $check_user->id,
                            'verify_token' => $verify_token,
                        ];

                        DBHelpers::update_query_v2(
                            AppUser::class,
                            ['verify_token' => $verify_token],
                            $check_user->id
                        );

                        \Mail::to($request->email)->send(
                            new \App\Mail\UserEmailVerification($data)
                        );

                        return ResponseHelper::error_response(
                            'Email not verified yet, verification link sent to your email',
                            null,
                            401
                        );
                    }

                    return ResponseHelper::success_response(
                        'Login Successful',
                        $user,
                        $token
                    );
                } else {
                    return ResponseHelper::error_response(
                        'Invalid login credentials',
                        null,
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['email', 'password'];
                $error_res = ErrorValidation::arrange_error($errors, $props);
                return ResponseHelper::error_response(
                    'validation error',
                    $error_res,
                    401
                );
            }
        } else {
            return ResponseHelper::error_response(
                'HTTP Request not allowed',
                '',
                404
            );
        }
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth()
                    ->factory()
                    ->getTTL() * 60,
        ]);
    }
}
