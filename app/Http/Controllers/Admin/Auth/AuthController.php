<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Validations\Admin\AdminAuthValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Admin\AdminUser;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AdminAuthValidator::validate_rules(
                $request,
                'register'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $data = [
                        'full_name' => $request->full_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'username' => $request->username,
                        'password' => bcrypt($request->password),
                    ];

                    $register = DBHelpers::create_query(
                        AdminUser::class,
                        $data
                    );

                    if ($register) {
                        if (
                            $token = Auth::guard('web-api')->attempt([
                                'email' => $request->email,
                                'password' => $request->password,
                            ])
                        ) {
                            $token = $this->respondWithToken($token);
                            $user = $this->me();

                            return ResponseHelper::success_response(
                                'Registration was Successful',
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
                    'username',
                    'password',
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
            $validate = AdminAuthValidator::validate_rules($request, 'login');

            if (!$validate->fails() && $validate->validated()) {
                if (
                    $token = Auth::guard('web-api')->attempt([
                        'email' => $request->email,
                        'password' => $request->password,
                    ])
                ) {
                    $token = $this->respondWithToken($token);
                    $user = DBHelpers::with_where_query_filter_first(
                        AdminUser::class,
                        ['resturant'],
                        [
                            'email' => $request->email,
                        ]
                    );
                    $user = response()->json($user);

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

    public function reset_token(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AdminAuthValidator::validate_rules(
                $request,
                'reset_token'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    DBHelpers::exists(AdminUser::class, [
                        'email' => $request->email,
                    ])
                ) {
                    DBHelpers::update_query_v3(
                        AdminUser::class,
                        ['reset_password_token' => null],
                        ['email' => $request->email]
                    );

                    return ResponseHelper::success_response(
                        'Reset token successfully',
                        null,
                        null
                    );
                } else {
                    return ResponseHelper::error_response(
                        'Email not found',
                        null,
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['email'];
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

    public function reset_password(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AdminAuthValidator::validate_rules(
                $request,
                'reset_password'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    DBHelpers::exists(AdminUser::class, [
                        'email' => $request->email,
                    ])
                ) {
                    $admin = DBHelpers::query_filter_first(AdminUser::class, [
                        'email' => $request->email,
                    ]);
                    if ($admin->reset_password_token != $request->token) {
                        return ResponseHelper::error_response(
                            'Invalid token',
                            null,
                            401
                        );
                    }

                    DBHelpers::update_query_v3(
                        AdminUser::class,
                        ['password' => bcrypt($request->password)],
                        ['email' => $request->email]
                    );

                    DBHelpers::update_query_v3(
                        AdminUser::class,
                        ['reset_password_token' => null],
                        ['email' => $request->email]
                    );

                    return ResponseHelper::success_response(
                        'Reset password successfully',
                        null,
                        null
                    );
                } else {
                    return ResponseHelper::error_response(
                        'Email not found',
                        null,
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['email', 'password', 'token'];
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

    public function validate_email(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AdminAuthValidator::validate_rules(
                $request,
                'validate_email'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    DBHelpers::exists(AdminUser::class, [
                        'email' => $request->email,
                    ])
                ) {
                    $token = Func::generate_reference(5, 'numeric');
                    DBHelpers::update_query_v3(
                        AdminUser::class,
                        ['reset_password_token' => $token],
                        ['email' => $request->email]
                    );

                    $jobMailData = [
                        'token' => $token,
                    ];

                    try {
                        //code...
                        \Mail::to($request->email)->send(
                            new \App\Mail\AdminResetPasswordToken($jobMailData)
                        );
                    } catch (\Throwable $th) {
                        //throw $th;
                    }



                    return ResponseHelper::success_response(
                        'Reset password token sent to your mail, token will expiry in the next 5 minutes',
                        null,
                        null
                    );
                } else {
                    return ResponseHelper::error_response(
                        'Email not found',
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
        $user = auth()->hasUser();
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
