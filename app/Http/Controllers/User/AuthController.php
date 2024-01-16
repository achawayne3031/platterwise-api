<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Validations\UserAuthValidator;
use App\Validations\ErrorValidation;

use App\Helpers\ResponseHelper;
use App\Models\User\AppUser;
use App\Models\RestaurantTeam;

use App\Helpers\DBHelpers;
use App\Helpers\Func;

class AuthController extends Controller
{
    //

    public function team_login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserAuthValidator::validate_rules($request, 'login');

            /// $verify_token = Func::generate_reference(10, 'all');

            if (!$validate->fails() && $validate->validated()) {
                if (
                    $token = Auth::guard('team-api')->attempt([
                        'email' => $request->email,
                        'password' => $request->password,
                    ])
                ) {
                    $token = $this->respondWithToken($token);
                    $user = $this->me();
                    $check_user = auth()->user();

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
            $validate = UserAuthValidator::validate_rules(
                $request,
                'reset_password'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    DBHelpers::exists(AppUser::class, [
                        'email' => $request->email,
                    ])
                ) {
                    $admin = DBHelpers::query_filter_first(AppUser::class, [
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
                        AppUser::class,
                        [
                            'password' => bcrypt($request->password),
                            'reset_password_token' => null,
                        ],
                        ['email' => $request->email]
                    );

                    // DBHelpers::update_query_v3(
                    //     AppUser::class,
                    //     ['reset_password_token' => null],
                    //     ['email' => $request->email]
                    // );

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
            $validate = UserAuthValidator::validate_rules(
                $request,
                'validate_email'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    DBHelpers::exists(AppUser::class, [
                        'email' => $request->email,
                    ])
                ) {
                    $token = Func::generate_reference(5, 'numeric');
                    DBHelpers::update_query_v3(
                        AppUser::class,
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

                        try {
                            //code...

                            \Mail::to($request->email)->send(
                                new \App\Mail\UserEmailVerification($data)
                            );
                        } catch (\Throwable $th) {
                            //throw $th;
                        }

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

                        try {
                            //code...
                            \Mail::to($request->email)->send(
                                new \App\Mail\UserEmailVerification($data)
                            );
                        } catch (\Throwable $th) {
                            //throw $th;
                        }

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
