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
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //

    public function view_reservation(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'view_reservation'
            );

            if (!$validate->fails() && $validate->validated()) {
                $reservation = DBHelpers::with_where_query_filter(
                    Reservation::class,
                    ['reservation_bill', 'restaurant', 'owner'],
                    ['id' => $request->reservation_id]
                );

                return ResponseHelper::success_response(
                    'View reservation',
                    $reservation
                );
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

    public function weekly_reservation_count(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'weekly_reservation_count'
            );

            if (!$validate->fails() && $validate->validated()) {
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

                    global $mon_amount;
                    global $tue_amount;
                    global $wed_amount;
                    global $thur_amount;
                    global $fri_amount;
                    global $sat_amount;
                    global $sun_amount;

                    $mon_amount = 0;
                    $tue_amount = 0;
                    $wed_amount = 0;
                    $thur_amount = 0;
                    $fri_amount = 0;
                    $sat_amount = 0;
                    $sun_amount = 0;

                    $mon = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isMonday());

                    $re = [];
                    $rese = [];

                    foreach ($mon as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $mon_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', '=', $fir[0])
                            ->count();

                        $mon_amount += $mon_amount_count;
                    }

                    $tue = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isTuesday());
                    foreach ($tue as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $tue_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $tue_amount += $tue_amount_count;
                    }

                    $wed = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isWednesday());
                    foreach ($wed as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $wed_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $wed_amount += $wed_amount_count;
                    }

                    $thur = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isThursday());
                    foreach ($thur as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $thur_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $thur_amount += $thur_amount_count;
                    }

                    $fri = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isFriday());
                    foreach ($fri as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $fri_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $fri_amount += $fri_amount_count;
                    }

                    $sat = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isSaturday());
                    foreach ($sat as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $sat_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $sat_amount += $sat_amount_count;
                    }

                    $sun = CarbonPeriod::between(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    )->filter(fn($date) => $date->isSunday());
                    foreach ($sun as $value) {
                        $ex = explode('T', $value);
                        $fir = explode(' ', $ex[0]);

                        $sun_amount_count = Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->whereDate('created_at', $fir[0])
                            ->count();

                        $sun_amount += $sun_amount_count;
                    }

                    $weekly_month = [
                        'mon' => $mon_amount,
                        'tue' => $tue_amount,
                        'wed' => $wed_amount,
                        'thur' => $thur_amount,
                        'fri' => $fri_amount,
                        'sat' => $sat_amount,
                        'sun' => $sun_amount,
                    ];

                    $data = [
                        'weekly_reservations' => $weekly_month,
                    ];

                    return ResponseHelper::success_response(
                        'Reservation dashboard analysis data fetched was successfully',
                        $data
                    );
                } catch (\Throwable $error) {
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

                DBHelpers::update_query_v3(
                    Reservation::class,
                    ['status' => 5],
                    [
                        'id' => $request->reservation_id,
                    ]
                );

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

                        try {
                            //code...

                            \Mail::to($owner_email)->send(
                                new \App\Mail\ReservationConfirmed($mailData)
                            );
                        } catch (\Throwable $th) {
                            //throw $th;
                        }

                        ///  $guests = json_decode($res_data->guests);

                        // if (count($guests) > 0) {
                        //     $jobMailData = [
                        //         'owner_name' => $res_data->owner->full_name,
                        //         'restaurant' => $res_data->restaurant->name,
                        //         'seat_type' => $res_data->seat_type,
                        //         'guests' => $guests,
                        //         'location' => $res_data->restaurant->address,
                        //         'book_date' => $booked_date,
                        //         'book_time' => $book_time,
                        //     ];

                        //     $job = (new \App\Jobs\SendDinnerInvite(
                        //         $jobMailData
                        //     ))->delay(now()->addSeconds(2));

                        //     dispatch($job);
                        // }

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

                DBHelpers::update_query_v2(
                    Reservation::class,
                    ['status' => 2],
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

                    try {
                        //code...

                        $job = (new \App\Jobs\SendDinnerInvite(
                            $jobMailData
                        ))->delay(now()->addSeconds(2));

                        dispatch($job);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
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

                try {
                    //code...

                    \Mail::to($owner_email)->send(
                        new \App\Mail\ReservationCancelled($mailData)
                    );
                } catch (\Throwable $th) {
                    //throw $th;
                }

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
