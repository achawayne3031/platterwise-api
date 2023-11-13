<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\ResponseHelper;
use App\Models\Resturant;
use App\Models\Reservation;
use App\Models\Transactions;

use App\Models\RestaurantFollowers;
use App\Helpers\DBHelpers;
use App\Validations\User\RestaurantValidator;
use App\Validations\User\RestaurantFollowerValidator;
use App\Validations\User\TransactionsValidator;

use App\Validations\ErrorValidation;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedRestaurant;
use App\Models\RestaurantReviews;

class TransactionController extends Controller
{
    //

    public function reservation(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = TransactionsValidator::validate_rules(
                $request,
                'reservation'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $uid = Auth::id();
                    $owner = Auth::user();

                    if (
                        !DBHelpers::exists(Reservation::class, [
                            'id' => $request->reservation_id,
                            'uid' => $uid,
                        ])
                    ) {
                        return ResponseHelper::error_response(
                            'Reservation not found on your collection',
                            null,
                            401
                        );
                    }

                    $data = DBHelpers::data_with_where_paginate(
                        Transactions::class,
                        ['reservation_id' => $request->reservation_id],
                        ['restaurant', 'reservation']
                    );

                    return ResponseHelper::success_response(
                        'Reservation transactions fetched was successfully',
                        $data
                    );
                } catch (\Throwable $error) {
                    return ResponseHelper::error_response($error);
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['reservation_id'];
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
}
