<?php

namespace App\Http\Controllers\Team\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Validations\Team\AuthValidators;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\User\AppUser;
use App\Models\RestaurantTeam;
use App\Helpers\DBHelpers;
use App\Helpers\Func;
use App\Models\Resturant;

class AuthController extends Controller
{
    //

    public function test()
    {
        return 'hello';
    }

    public function team_login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AuthValidators::validate_rules($request, 'login');

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

    public function restaurant($restaurant_id)
    {
        return DBHelpers::query_filter_first(Resturant::class, [
            'restaurant_id' => $restaurant_id,
        ]);
    }

    public function me()
    {
        $user = auth('team-api')->user();
        $user->restaurant = DBHelpers::query_filter_first(Resturant::class, [
            'id' => $user->restaurant_id,
        ]);

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
