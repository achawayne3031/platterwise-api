<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DBHelpers;
use App\Models\User\AppUser;

class VerificationController extends Controller
{
    //

    public function verify_user($user, $token)
    {
        $status = false;

        if (!DBHelpers::exists(AppUser::class, ['id' => $user])) {
            return view('user.VerifyUserStatusPage')->with([
                'status' => $status,
            ]);
        }

        $current_user = DBHelpers::query_filter_first(AppUser::class, [
            'id' => $user,
        ]);

        if (
            $current_user->verify_token != null &&
            $current_user->verify_token == $token
        ) {
            $status = true;
            DBHelpers::update_query_v2(
                AppUser::class,
                ['is_verified' => 1],
                $user
            );
        }

        return view('user.VerifyUserStatusPage')->with(['status' => $status]);
    }
}
