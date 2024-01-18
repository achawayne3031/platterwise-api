<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Validations\Team\ReservationValidators;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\User\AppUser;
use App\Models\RestaurantTeam;
use App\Helpers\DBHelpers;
use App\Helpers\Func;
use App\Models\Resturant;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationController extends Controller
{
    //

    public function check_in(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidators::validate_rules(
                $request,
                'check_in'
            );

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('team-api')->user();
                $team_id = $user->id;

                if (
                    !DBHelpers::exists(RestaurantTeam::class, [
                        'id' => $team_id,
                        'restaurant_id' => $request->restaurant_id,
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

    public function view_reservation($id)
    {
        if (!DBHelpers::exists(Reservation::class, ['id' => $id])) {
            return ResponseHelper::error_response(
                'Reservation not found',
                null,
                401
            );
        }

        $reservation = DBHelpers::with_where_query_filter_first(
            Reservation::class,
            ['restaurant', 'owner', 'reservation_bill'],
            ['id' => $id]
        );

        return ResponseHelper::success_response(
            'Current reservations fetched successfully',
            $reservation
        );
    }

    public function pending_reservations(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidators::validate_rules(
                $request,
                'reservations'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $pending_reservations = DBHelpers::data_with_where_paginate(
                        Reservation::class,
                        [
                            'restaurant_id' => $request->restaurant_id,
                            'status' => 1,
                        ],
                        ['restaurant'],
                        40
                    );

                    return ResponseHelper::success_response(
                        'All pending reservations team',
                        $pending_reservations
                    );
                } catch (Exception $e) {
                    return ResponseHelper::error_response(
                        'Server Error',
                        $e->getMessage(),
                        401
                    );
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
