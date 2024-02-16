<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\ReservationValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Reservation;
use App\Models\ReservationSplitBills;

use App\Models\ReservationBills;

use App\Models\Transactions;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

use Illuminate\Support\Facades\Auth;
use App\Services\Paystack;

class ReservationController extends Controller
{
    //

    public function get_reservation_bills(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'get_reservation_bills'
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

                    $data = DBHelpers::where_query(ReservationBills::class, [
                        'reservation_id' => $request->reservation_id,
                    ]);

                    return ResponseHelper::success_response(
                        'Get reservation bills was successful',
                        $data
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

    public function get_split_bills(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ReservationValidator::validate_rules(
                $request,
                'get_split_bills'
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

                    $data = DBHelpers::where_query(
                        ReservationSplitBills::class,
                        [
                            'reservation_id' => $request->reservation_id,
                        ]
                    );

                    return ResponseHelper::success_response(
                        'Get reservation split bills was successful',
                        $data
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
                        $split_bills = DBHelpers::where_query(
                            ReservationSplitBills::class,
                            ['reservation_id' => $request->reservation_id]
                        );

                        return ResponseHelper::error_response(
                            'Reservation has been splitted already',
                            $split_bills,
                            401
                        );
                    }

                    $reservation_id = $request->reservation_id;
                    $reservation_data = DBHelpers::with_where_query_filter_first(
                        Reservation::class,
                        ['restaurant'],
                        ['id' => $reservation_id]
                    );
                    $resturant_data = $reservation_data->restaurant;
                    $resturant_name = $reservation_data->restaurant->name;

                    if (count($request->guests) > 1) {
                        $dispatchData = [
                            'restaurant_id' => $resturant_data->id,
                            'restuarant' => $resturant_data,
                            'restaurant_name' => $resturant_name,
                            'guests' => $request->guests,
                            'reservation_id' => $request->reservation_id,
                            'total_amount' => $request->total_amount,
                        ];

                        try {
                            //code...

                            $job = (new \App\Jobs\SendBillPayment(
                                $dispatchData
                            ))->delay(now());

                            dispatch($job);
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    } else {
                        if (count($request->guests) > 0) {
                            $current_guest = $request->guests[0];

                            $payment_ref = Func::generate_reference(20);

                            $post_data = [
                                'email' => $current_guest['guest_email'],
                                'amount' => $current_guest['bill'] * 100,
                                'callback_url' =>
                                    'https://api2.platterwise.com/verify-payment/' .
                                    $payment_ref,
                            ];

                            $paystack = Paystack::intializeTransaction(
                                $post_data
                            );

                            if ($paystack->status) {
                                $auth_url = $paystack->data->authorization_url;
                                $access_code = $paystack->data->access_code;
                                $reference = $paystack->data->reference;

                                $desc =
                                    $current_guest['guest_name'] .
                                    ' Payment of  ' .
                                    $current_guest['bill'];

                                $transaction_data = [
                                    'restaurant_id' => $resturant_data->id,
                                    'reservation_id' =>
                                        $request->reservation_id,
                                    'email' => $current_guest['guest_email'],
                                    'guest_name' =>
                                        $current_guest['guest_name'],
                                    'payment_type' => 'card',
                                    'description' => $desc,
                                    'ref' => $reference,
                                    'amount' => $current_guest['bill'] * 100,
                                    'init_extra' => json_encode(
                                        $paystack->data
                                    ),
                                    'payment_ref' => $payment_ref,
                                ];

                                DBHelpers::create_query(
                                    Transactions::class,
                                    $transaction_data
                                );

                                $set_guest = [];

                                $in_guest = [
                                    'guest_email' =>
                                        $current_guest['guest_email'],
                                    'guest_name' =>
                                        $current_guest['guest_name'],
                                    'type' => $current_guest['type'],
                                    'bill' => $current_guest['bill'],
                                    'payment_url' => $auth_url,
                                    'amount_paid' => 0,
                                ];

                                array_push($set_guest, $in_guest);

                                $create_data = [
                                    'reservation_id' =>
                                        $request->reservation_id,
                                    'total_amount' => $request->total_amount,
                                    'guests' => json_encode($set_guest),
                                ];

                                DBHelpers::create_query(
                                    ReservationSplitBills::class,
                                    $create_data
                                );

                                if ($current_guest['type'] == 'owner') {
                                    return ResponseHelper::success_response(
                                        'Reservation split bills was successful',
                                        $paystack->data
                                    );
                                } else {
                                    $jobMailData = [
                                        'payment_link' => $auth_url,

                                        'restaurant' => $resturant_data,
                                        'restaurant_name' => $resturant_name,
                                        'guest_name' =>
                                            $current_guest['guest_email'],
                                        'amount' => $current_guest['bill'],
                                    ];

                                    try {
                                        //code...

                                        \Mail::to(
                                            $current_guest['guest_email']
                                        )->send(
                                            new \App\Mail\BillPayment(
                                                $jobMailData
                                            )
                                        );
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                    }
                                }
                            }
                        }
                    }

                    DBHelpers::update_query_v3(
                        Reservation::class,
                        ['is_splitted' => 1],
                        [
                            'id' => $request->reservation_id,
                        ]
                    );

                    return ResponseHelper::success_response(
                        'Reservation split bills was successful',
                        null
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

                    $reservation_code =
                        Func::generate_reference(3, 'upper') .
                        Func::generate_reference(10, 'numeric');

                    $create_data = [
                        'uid' => Auth::id(),
                        'reservation_date' => $request->reservation_date,
                        'restaurant_id' => $request->restaurant_id,
                        'seat_type' => $request->seat_type,
                        'guests' => json_encode($request->guest),
                        'all_guests' => json_encode($all_guest_data),
                        'guest_no' => count($all_guest_data),
                        'code' => $reservation_code,
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
