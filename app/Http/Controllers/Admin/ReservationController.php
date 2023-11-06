<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Validations\Admin\ReservationValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Resturant;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantImages;
use App\Helpers\DBHelpers;
use App\Models\Reservation;
use App\Models\ReservationBills;
use Carbon\Carbon;
use App\Models\User\AppUser;

use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //

    public function create_bill(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'create_bill'
            );

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;
                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

                if (
                    DBHelpers::exists(ReservationBills::class, [
                        'reservation_id' => $request->reservation_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation bill has been created already',
                        null,
                        401
                    );
                }

                $create = ReservationBills::create($request->all());

                if ($create) {
                    return ResponseHelper::success_response(
                        'Reservation approved was successfully',
                        null
                    );
                } else {
                    return ResponseHelper::error_response(
                        'Update failed, Database insertion issues',
                        $validate->errors(),
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['reservation_id', 'total_bill', 'set_picture'];
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

    public function check_in(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'check_in'
            );

            if (!$validate->fails() && $validate->validated()) {
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

                $current_reservation = DBHelpers::query_filter_first(
                    Reservation::class,
                    [
                        'id' => $request->reservation_id,
                        'restaurant_id' => $request->restaurant_id,
                    ]
                );

                $status = $current_reservation->status;

                switch ($status) {
                    case 0:
                        # code...
                        return ResponseHelper::error_response(
                            'Reservation has been cancelled already',
                            null,
                            401
                        );
                        break;

                    case 1:
                        # code...
                        return ResponseHelper::error_response(
                            'Reservation has not been approved',
                            null,
                            401
                        );
                        break;

                    case 2:
                        # code...

                        DBHelpers::update_query_v3(
                            Reservation::class,
                            ['status' => 3],
                            [
                                'id' => $request->reservation_id,
                                'restaurant_id' => $request->restaurant_id,
                            ]
                        );

                        $res_data = DBHelpers::with_where_query_filter_first(
                            Reservation::class,
                            ['owner', 'restaurant'],
                            [
                                'id' => $request->reservation_id,
                                'restaurant_id' => $request->restaurant_id,
                            ]
                        );

                        $res_date = $res_data->reservation_date;
                        $cre = Carbon::create($res_date);
                        $formattedTime = $cre->toDayDateTimeString();
                        $array_time = explode(' ', $formattedTime);
                        $booked_date = $cre->toFormattedDateString();
                        $book_time = $array_time[4] . ' ' . $array_time[5];

                        $mailData = [
                            'owner_name' => $res_data->owner->full_name,
                            'restaurant' => $res_data->restaurant->name,
                            'seat_type' => $res_data->seat_type,
                            'guest' => $res_data->guest_no,
                            'location' => $res_data->restaurant->address,
                            'book_date' => $booked_date,
                            'book_time' => $book_time,
                        ];

                        $owner_email = $res_data->owner->email;

                        \Mail::to($owner_email)->send(
                            new \App\Mail\ReservationConfirmed($mailData)
                        );

                        $guests = json_decode($res_data->guests);

                        if (count($guests) > 0) {
                            $jobMailData = [
                                'owner_name' => $res_data->owner->full_name,
                                'restaurant' => $res_data->restaurant->name,
                                'seat_type' => $res_data->seat_type,
                                'guests' => $guests,
                                'location' => $res_data->restaurant->address,
                                'book_date' => $booked_date,
                                'book_time' => $book_time,
                            ];

                            $job = (new \App\Jobs\SendDinnerInvite(
                                $jobMailData
                            ))->delay(now()->addSeconds(2));

                            dispatch($job);
                        }

                        return ResponseHelper::success_response(
                            'Reservation checked in was successful',
                            null
                        );

                        break;

                    case 3:
                        # code...
                        return ResponseHelper::error_response(
                            'Reservation is in progress already',
                            null,
                            401
                        );
                        break;

                    case 4:
                        # code...
                        return ResponseHelper::error_response(
                            'Reservation has been completed already',
                            null,
                            401
                        );
                        break;

                    default:
                        # code...
                        break;
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'reservation_id'];
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

    public function close(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'approve'
            );

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;
                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

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

                if (
                    DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been cancelled already',
                        null,
                        401
                    );
                }

                if (
                    DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'status' => 2,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been approved already',
                        null,
                        401
                    );
                }

                DBHelpers::update_query_v2(
                    Reservation::class,
                    ['status' => 2],
                    $request->reservation_id
                );

                return ResponseHelper::success_response(
                    'Reservation approved was successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'reservation_id'];
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

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules($request, 'edit');

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;
                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

                if (
                    !DBHelpers::exists(Resturant::class, [
                        'id' => $request->restaurant_id,
                        'admin_uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant not found on your collection',
                        null,
                        401
                    );
                }

                if (
                    !DBHelpers::exists(Reservation::class, [
                        'restaurant_id' => $request->restaurant_id,
                        'id' => $request->reservation_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation not found',
                        null,
                        401
                    );
                }

                DBHelpers::update_query_v3(
                    Reservation::class,
                    ['reservation_date' => $request->reservation_date],
                    [
                        'restaurant_id' => $request->restaurant_id,
                        'id' => $request->reservation_id,
                    ]
                );

                return ResponseHelper::success_response(
                    'Reservation update was successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = [
                    'restaurant_id',
                    'reservation_id',
                    'reservation_date',
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

    public function approve(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'approve'
            );

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;

                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

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

                if (
                    !DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'restaurant_id' => $request->restaurant_id,
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
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been cancelled already',
                        null,
                        401
                    );
                }

                if (
                    DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'status' => 2,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been approved already',
                        null,
                        401
                    );
                }

                // DBHelpers::update_query_v2(
                //     Reservation::class,
                //     ['status' => 2],
                //     $request->reservation_id
                // );

                $res_data = DBHelpers::with_where_query_filter_first(
                    Reservation::class,
                    ['owner', 'restaurant'],
                    [
                        'id' => $request->reservation_id,
                        'restaurant_id' => $request->restaurant_id,
                    ]
                );

                $res_date = $res_data->reservation_date;
                $cre = Carbon::create($res_date);
                $formattedTime = $cre->toDayDateTimeString();
                $array_time = explode(' ', $formattedTime);
                $booked_date = $cre->toFormattedDateString();
                $book_time = $array_time[4] . ' ' . $array_time[5];

                $guests = json_decode($res_data->guests);

                if (count($guests) > 0) {
                    $jobMailData = [
                        'owner_name' => $res_data->owner->full_name,
                        'restaurant' => $res_data->restaurant->name,
                        'seat_type' => $res_data->seat_type,
                        'guests' => $guests,
                        'location' => $res_data->restaurant->address,
                        'book_date' => $booked_date,
                        'book_time' => $book_time,
                    ];

                    $job = (new \App\Jobs\SendDinnerInvite(
                        $jobMailData
                    ))->delay(now()->addSeconds(2));

                    dispatch($job);
                }

                return ResponseHelper::success_response(
                    'Reservation approved was successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'reservation_id'];
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

    public function cancel(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'cancel'
            );

            if (!$validate->fails() && $validate->validated()) {
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

                if (
                    DBHelpers::exists(Reservation::class, [
                        'id' => $request->reservation_id,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Reservation has been cancelled already',
                        null,
                        401
                    );
                }

                DBHelpers::update_query_v2(
                    Reservation::class,
                    ['status' => 0, 'cancel_reason' => $request->cancel_reason],
                    $request->reservation_id
                );

                $res_data = DBHelpers::with_where_query_filter_first(
                    Reservation::class,
                    ['owner', 'restaurant'],
                    [
                        'id' => $request->reservation_id,
                        'restaurant_id' => $request->restaurant_id,
                    ]
                );

                $mailData = [
                    'owner_name' => $res_data->owner->full_name,
                    'restaurant' => $res_data->restaurant->name,
                ];

                $owner_email = $res_data->owner->email;

                \Mail::to($owner_email)->send(
                    new \App\Mail\ReservationCancelled($mailData)
                );

                return ResponseHelper::success_response(
                    'Reservation cancelled was successful',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'reservation_id', 'cancel_reason'];
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

    public function all(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules($request, 'all');

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;
                if (
                    !DBHelpers::exists(Resturant::class, ['admin_uid' => $uid])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant not found on your collection',
                        null,
                        401
                    );
                }

                $reservation = DBHelpers::data_with_where_paginate(
                    Reservation::class,
                    ['restaurant_id' => $request->restaurant_id],
                    ['owner'],
                    40
                );

                return ResponseHelper::success_response(
                    'Reservations fetched successfully',
                    $reservation
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

        $restaurant = DBHelpers::data_where_paginate(
            Resturant::class,
            ['admin_uid' => $uid],
            40
        );

        return ResponseHelper::success_response(
            'Restaurant created',
            restaurant
        );
    }
}
