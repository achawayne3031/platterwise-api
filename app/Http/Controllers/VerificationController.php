<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DBHelpers;
use App\Models\User\AppUser;
use App\Services\Paystack;
use App\Models\Transactions;

class VerificationController extends Controller
{
    //

    public function verify_payment(Request $request, $paymentRef)
    {
        $status = 1;

        $paystack_ref = $request->query('reference');

        $verify_res = Paystack::verifyTransaction($paystack_ref);

        $payment_status = $verify_res->data->status;

        switch ($payment_status) {
            case 'success':
                # code...
                $status = 3;

                $payment_data = json_encode($verify_res->data);

                DBHelpers::update_query_v3(
                    Transactions::class,
                    ['payment_extra' => $payment_data],
                    ['ref' => $paystack_ref]
                );

                /// 'status' => 3,

                break;

            default:
                # code...
                break;
        }

        return view('user.VerifyPaymentStatus')->with(['status' => $status]);
    }

    public function verify_user($user, $token)
    {
        // 0 = not found
        // 1 = success
        // 2 = invalid token
        /// 3 = verified already

        $status = 0;

        if (!DBHelpers::exists(AppUser::class, ['id' => $user])) {
            $status = 0;
            return view('user.VerifyUserStatusPage')->with([
                'status' => $status,
            ]);
        }

        $current_user = DBHelpers::query_filter_first(AppUser::class, [
            'id' => $user,
        ]);

        if ($current_user->is_verified == 1) {
            $status = 3;
            return view('user.VerifyUserStatusPage')->with([
                'status' => $status,
            ]);
        }

        if (
            $current_user->verify_token != null &&
            $current_user->verify_token == $token
        ) {
            $status = 1;
            DBHelpers::update_query_v2(
                AppUser::class,
                ['is_verified' => 1, 'verify_token' => null],
                $user
            );
        } else {
            $status = 2;
        }

        return view('user.VerifyUserStatusPage')->with(['status' => $status]);
    }
}
