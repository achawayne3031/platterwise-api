<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\Admin\AdminAuthValidator;
use App\Validations\Admin\ResturantValidator;

use App\Validations\Admin\TransactionsValidator;

use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Admin\AdminUser;
use App\Models\Resturant;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantImages;
use App\Models\RestaurantReviews;
use App\Models\Reservation;
use App\Models\Transactions;
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                DB::beginTransaction();

                try {
                    $user = auth('web-api')->user();
                    $uid = $user->id;

                    // if (
                    //     !DBHelpers::exists(Resturant::class, [
                    //         'admin_uid' => $uid,
                    //         'id' => $request->restaurant_id,
                    //     ])
                    // ) {
                    //     return ResponseHelper::error_response(
                    //         'Restaurant not found on your collection',
                    //         null,
                    //         401
                    //     );
                    // }

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
                    DB::rollBack(); // rollback in case of an exception || error
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

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = TransactionsValidator::validate_rules(
                $request,
                'index'
            );

            if (!$validate->fails() && $validate->validated()) {
                DB::beginTransaction();

                try {
                    $user = auth('web-api')->user();
                    $uid = $user->id;

                    if (
                        !DBHelpers::exists(Resturant::class, [
                            'admin_uid' => $uid,
                            'id' => $request->restaurant_id,
                        ])
                    ) {
                        return ResponseHelper::error_response(
                            'Restaurant not found on your collection',
                            null,
                            401
                        );
                    }

                    $data = DBHelpers::data_with_where_paginate(
                        Transactions::class,
                        ['restaurant_id' => $request->restaurant_id],
                        ['restaurant']
                    );

                    return ResponseHelper::success_response(
                        'Restaurant transactions fetched was successfully',
                        $data
                    );
                } catch (\Throwable $error) {
                    DB::rollBack(); // rollback in case of an exception || error
                    return ResponseHelper::error_response($error);
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id'];
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
