<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use App\Models\User\AppUser;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

class VerifiedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $check_user = Auth::user();
        $verify_token = Func::generate_reference(10, 'all');

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
                \Mail::to($check_user->email)->send(
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

        return $next($request);
    }
}
