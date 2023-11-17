<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\Admin\AdminAuthValidator;
use App\Validations\Admin\ResturantValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Admin\AdminUser;
use App\Models\Resturant;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantImages;
use App\Models\RestaurantReviews;
use App\Models\Reservation;
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResturantController extends Controller
{
    //

    public function edit_restaurant(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'edit');

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;

                /// cover pic
                /// https://firebasestorage.googleapis.com/v0/b/platterwise.appspot.com/o/restaurant%2Fannie-spratt-oT7_v-I0hHg-unsplash.jpg446aef1c-66df-489c-a6de-f6195d0a6618?alt=media&token=1dfd1b0f-15dc-49da-8f99-78a4df2fa521?1697918398114

                // banner
                // https://firebasestorage.googleapis.com/v0/b/platterwise.appspot.com/o/restaurant%2Fmadie-hamilton-dZ-HI4EuWcA-unsplash.jpg54b9cd27-cd21-48de-89e7-1be1f046d1fc?alt=media&token=4f0ff4d6-6dd5-4470-8748-9ac99a46a385?1697918352958

                // address
                // Ago Palace Way, Lagos, Nigeria

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

                try {
                    DB::beginTransaction();

                    $records = Resturant::where([
                        'id' => $request->restaurant_id,
                        'admin_uid' => $uid,
                    ]);

                    $records->update(
                        $request->only([
                            'name',
                            'phone',
                            'address',
                            'state',
                            'descriptions',
                            'opening_hour',
                            'closing_hour',
                            'banner',
                            'cover_pic',
                        ])
                    );

                    // if (count($request->seat_type) > 0) {
                    //     DBHelpers::delete_query_multi(
                    //         RestaurantSeatType::class,
                    //         ['restaurant_id' => $request->restaurant_id]
                    //     );

                    //     $seat = $request->seat_type;
                    //     foreach ($seat as $value) {
                    //         RestaurantSeatType::create([
                    //             'name' => $value['name'],
                    //             'restaurant_id' => $request->restaurant_id,
                    //         ]);
                    //     }
                    // }

                    DB::commit(); // execute the operations above and commit transaction

                    return ResponseHelper::success_response(
                        'Restaurant updated successfully',
                        null
                    );
                } catch (\Throwable $error) {
                    DB::rollBack(); // rollback in case of an exception || error
                    return ResponseHelper::error_response($error);
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = [
                    'name',
                    'email',
                    'address',
                    'state',
                    'phone',
                    'cover_pic',
                    'banner',
                    'descriptions',
                    'working_days',
                    'closing_hour',
                    'opening_hour',
                    'website',
                    'longitude',
                    'latitude',
                    'seat_type',
                    'social_handle',
                    'days',
                    'restaurant_id',
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

    public function dashboard(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules(
                $request,
                'dashboard'
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

                    $data = [
                        'total_pending_reservation' => Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->pending()
                            ->count(),
                        'total_rejected_reservation' => Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->rejected()
                            ->count(),
                        'total_accepted_reservation' => Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->accepted()
                            ->count(),
                        'total_inprogress_reservation' => Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->inprogress()
                            ->count(),
                        'total_completed_reservation' => Reservation::where([
                            'restaurant_id' => $request->restaurant_id,
                        ])
                            ->completed()
                            ->count(),

                        'total_reviews' => DBHelpers::count(
                            RestaurantReviews::class,
                            ['restaurant_id' => $request->restaurant_id]
                        ),
                    ];

                    return ResponseHelper::success_response(
                        'Restaurant dashboard analysis data fetched was successfully',
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

    public function edit_menu_picture(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules(
                $request,
                'edit_restaurant_menu_pic'
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

                    if (count($request->menu_picture) > 0) {
                        $image = $request->menu_picture;
                        foreach ($image as $value) {
                            RestaurantImages::create([
                                'image_url' => $value['menu_pic'],
                                'restaurant_id' => $request->restaurant_id,
                            ]);
                        }
                    }

                    DB::commit(); // execute the operations above and commit transaction

                    return ResponseHelper::success_response(
                        'Restaurant menu picture edited successfully',
                        null
                    );
                } catch (\Throwable $error) {
                    DB::rollBack(); // rollback in case of an exception || error
                    return ResponseHelper::error_response($error);
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['menu_picture', 'restaurant_id'];
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

    public function delete_restaurant_menu_pic(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules(
                $request,
                'delete_restaurant_menu_pic'
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
                    !DBHelpers::exists(RestaurantImages::class, [
                        'restaurant_id' => $request->restaurant_id,
                        'id' => $request->id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant menu picture has been deleted already',
                        null,
                        401
                    );
                }

                DBHelpers::delete_query_multi(RestaurantImages::class, [
                    'restaurant_id' => $request->restaurant_id,
                    'id' => $request->id,
                ]);

                return ResponseHelper::success_response(
                    'Restaurant menu picture deleted successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'id'];
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

    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'delete');

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
                    DBHelpers::exists(Resturant::class, [
                        'id' => $request->restaurant_id,
                        'admin_uid' => $uid,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant has been deleted already',
                        null,
                        401
                    );
                }

                DBHelpers::update_query_v3(
                    Resturant::class,
                    ['status' => 0],
                    [
                        'id' => $request->restaurant_id,
                        'admin_uid' => $uid,
                    ]
                );

                return ResponseHelper::success_response(
                    'Restaurant deleted successfully',
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

    public function menu(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'menu');

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;

                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

                if (
                    DBHelpers::exists(Resturant::class, [
                        'id' => $request->restaurant_id,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant has been deletd',
                        null,
                        401
                    );
                }

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

                $menus = DBHelpers::data_where_paginate(
                    RestaurantImages::class,
                    ['restaurant_id' => $request->restaurant_id]
                );

                return ResponseHelper::success_response(
                    'Restaurant menus fetched was successfully',
                    $menus
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

    public function reviews(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'reviews');

            if (!$validate->fails() && $validate->validated()) {
                $user = auth('web-api')->user();
                $uid = $user->id;
                // \Mail::to('achawayne@gmail.com')->send(
                //     new \App\Mail\MailTester()
                // );

                if (
                    DBHelpers::exists(Resturant::class, [
                        'id' => $request->restaurant_id,
                        'status' => 0,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant has been deletd',
                        null,
                        401
                    );
                }

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

                $reviews = DBHelpers::data_with_where_paginate(
                    RestaurantReviews::class,
                    ['restaurant_id' => $request->restaurant_id],
                    ['user']
                );

                return ResponseHelper::success_response(
                    'Restaurant reviews fetched was successfully',
                    $reviews
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

    public function all_reservations()
    {
    }

    //// view restaurant ///
    public function view_restaurant(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'view');

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(Resturant::class, [
                        'id' => $request->restaurant_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant not found',
                        $validate->errors(),
                        401
                    );
                }

                $current = DBHelpers::with_where_query_filter_first(
                    Resturant::class,
                    ['menu_pic', 'seat_type'],
                    ['id' => $request->restaurant_id]
                );

                $reviews = DBHelpers::with_where_query_filter(
                    RestaurantReviews::class,
                    ['user'],
                    ['restaurant_id' => $request->restaurant_id]
                );
                $current->review = $reviews;

                return ResponseHelper::success_response(
                    'View restaurant',
                    $current
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

    public function all()
    {
        $user = auth('web-api')->user();
        $uid = $user->id;

        $restaurant = DBHelpers::data_where_paginate_active(
            Resturant::class,
            ['admin_uid' => $uid],
            40
        );

        return ResponseHelper::success_response('All restaurant', $restaurant);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
                DB::beginTransaction();

                try {
                    $user = auth('web-api')->user();
                    $uid = $user->id;

                    $restaurant_data = [
                        'admin_uid' => $uid,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'state' => $request->state,
                        'cover_pic' => $request->cover_pic,
                        'banner' => $request->banner,
                        'descriptions' => $request->descriptions,
                        'working_days' => $request->days,
                        'opening_hour' => $request->opening_hour,
                        'closing_hour' => $request->closing_hour,
                        'website' => $request->website,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                    ];

                    $create = Resturant::create($restaurant_data);
                    $restaurant_id = $create->id;

                    if (count($request->seat_type) > 0) {
                        $seat = $request->seat_type;
                        foreach ($seat as $value) {
                            RestaurantSeatType::create([
                                'name' => $value['name'],
                                'restaurant_id' => $restaurant_id,
                            ]);
                        }
                    }

                    if (count($request->menu_picture) > 0) {
                        $image = $request->menu_picture;
                        foreach ($image as $value) {
                            RestaurantImages::create([
                                'image_url' => $value['menu_pic'],
                                'restaurant_id' => $restaurant_id,
                            ]);
                        }
                    }

                    DB::commit(); // execute the operations above and commit transaction

                    return ResponseHelper::success_response(
                        'Restaurant created successfully',
                        null
                    );
                } catch (\Throwable $error) {
                    DB::rollBack(); // rollback in case of an exception || error
                    return ResponseHelper::error_response($error);
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = [
                    'name',
                    'email',
                    'address',
                    'state',
                    'phone',
                    'cover_pic',
                    'banner',
                    'descriptions',
                    'working_days',
                    'closing_hour',
                    'opening_hour',
                    'website',
                    'longitude',
                    'latitude',
                    'seat_type',
                    'menu_picture',
                    'social_handle',
                    'days',
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
