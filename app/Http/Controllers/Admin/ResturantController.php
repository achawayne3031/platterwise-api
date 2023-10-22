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
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResturantController extends Controller
{
    //

    public function menu(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'menu');

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

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
                $uid = Auth::id();

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

    public function view_restaurant()
    {
    }

    public function all()
    {
        $uid = Auth::id();

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

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
                DB::beginTransaction();

                try {
                    $uid = Auth::id();

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
