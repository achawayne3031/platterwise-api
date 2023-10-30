<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\ReservationValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Reservation;
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //

    public function all()
    {
        $uid = Auth::id();

        $restaurant = DBHelpers::data_with_where_paginate(
            Reservation::class,
            ['uid' => $uid],
            ['restaurant'],
            40
        );

        return ResponseHelper::success_response(
            'All reservations fetched successfully',
            $restaurant
        );
    }

    public function cancel(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'cancel'
            );

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();
                if (
                    !DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation not found',
                        null,
                        401
                    );
                }

                if (
                    DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'uid' => $uid,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been cancelled already',
                        null,
                        401
                    );
                }

                DBHelpers::update_query_v3(
                    Reservation::class,
                    ['status' => 0],
                    [
                        'uid' => $uid,
                        'id' => $request->reservation_id,
                    ]
                );

                return ResponseHelper::success_response(
                    'Reservation cancelled was successful',
                    null
                );
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

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'create'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $requestData = $request->all();
                    $requestData['uid'] = Auth::id();
                    $owner = Auth::user();

                    $all_guest_data = [];

                    $owner_data = [
                        'name' => $owner->full_name,
                        'email' => $owner->email,
                        'type' => 'owner',
                    ];

                    array_push($all_guest_data, $owner_data);

                    if (isset($request->guest) && count($request->guest) > 0) {
                        $guest_data = $request->guest;
                        foreach ($guest_data as $value) {
                            $value['type'] = 'guest';
                            array_push($all_guest_data, $value);
                        }
                    }

                    $create_data = [
                        'uid' => Auth::id(),
                        'reservation_date' => $request->reservation_date,
                        'restaurant_id' => $request->restaurant_id,
                        'seat_type' => $request->seat_type,
                        'guests' => json_encode($request->guest),
                        'all_guests' => json_encode($all_guest_data),
                    ];

                    $register = DBHelpers::create_query(
                        Reservation::class,
                        $create_data
                    );

                    if ($register) {
                        return ResponseHelper::success_response(
                            'Reservation created was successful',
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'Creation failed, Database insertion issues',
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
                    'reservation_date',
                    'uid',
                    'guest_no',
                    'restaurant_id',
                    'seat_type',
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
}
