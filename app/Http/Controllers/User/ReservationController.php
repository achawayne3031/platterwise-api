<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\ReservationValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Reservation;
use App\Models\ReservationSplitBills;
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //

    public function split_bills(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'split_bills'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $requestData = $request->all();
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

                    if (
                        DBHelpers::exists(ReservationSplitBills::class, [
                            'reservation_id' => $request->reservation_id,
                        ])
                    ) {
                        return ResponseHelper::error_response(
                            'Reservation has been splitted already',
                            null,
                            401
                        );
                    }

                    $create_data = [
                        'reservation_id' => $request->reservation_id,
                        'total_amount' => $request->total_amount,
                        'guests' => json_encode($request->guests),
                    ];

                    $register = DBHelpers::create_query(
                        ReservationSplitBills::class,
                        $create_data
                    );

                    if ($register) {
                        return ResponseHelper::success_response(
                            'Reservation split bills was successful',
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
                $props = ['reservation_id', 'guests', 'total_amount'];
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

    public function view($id)
    {
        $uid = Auth::id();

        if (!DBHelpers::exists(Reservation::class, ['id' => $id])) {
            return ResponseHelper::error_response(
                'Reservation not found',
                null,
                401
            );
        }

        if (
            !DBHelpers::exists(Reservation::class, ['id' => $id, 'uid' => $uid])
        ) {
            return ResponseHelper::error_response(
                'Reservation not found in your collection',
                null,
                401
            );
        }

        $reservation = DBHelpers::with_where_query_filter_first(
            Reservation::class,
            ['restaurant', 'owner', 'reservation_bill'],
            ['uid' => $uid, 'id' => $id]
        );

        return ResponseHelper::success_response(
            'Current reservations fetched successfully',
            $reservation
        );
    }

    public function all()
    {
        $uid = Auth::id();

        $restaurant = DBHelpers::data_with_where_paginate(
            Reservation::class,
            ['uid' => $uid],
            ['restaurant', 'owner'],
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
                        'guest_no' => count($all_guest_data),
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
