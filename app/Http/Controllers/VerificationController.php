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
                    ['status' => 3, 'payment_extra' => $payment_data],
                    ['ref' => $paystack_ref]
                );

                break;

            default:
                # code...
                break;
        }

        return view('user.VerifyPaymentStatus')->with(['status' => $status]);
    }

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
                ['is_verified' => 1, 'verify_token' => null],
                $user
            );
        }

        return view('user.VerifyUserStatusPage')->with(['status' => $status]);
    }
}
