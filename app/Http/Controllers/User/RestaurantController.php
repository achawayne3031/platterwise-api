<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Resturant;
use App\Models\RestaurantFollowers;
use App\Helpers\DBHelpers;
use App\Validations\User\RestaurantValidator;
use App\Validations\User\RestaurantFollowerValidator;
use App\Validations\ErrorValidation;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    //

    public function unfollow(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantFollowerValidator::validate_rules(
                $request,
                'unfollow'
            );

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

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

                if (
                    DBHelpers::exists(RestaurantFollowers::class, [
                        'id' => $request->restaurant_id,
                        'uid' => $uid,
                    ])
                ) {
                    DBHelpers::delete_query_multi(RestaurantFollowers::class, [
                        'id' => $request->restaurant_id,
                        'uid' => $uid,
                    ]);
                }

                return ResponseHelper::success_response(
                    'Restaurant unfollowed successfully',
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

    public function follow(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantFollowerValidator::validate_rules(
                $request,
                'follow'
            );

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

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

                if (
                    DBHelpers::exists(RestaurantFollowers::class, [
                        'id' => $request->restaurant_id,
                        'uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant followed already',
                        $validate->errors(),
                        401
                    );
                }

                $requestData = $request->all();
                $requestData['uid'] = Auth::id();

                DBHelpers::create_query(
                    RestaurantFollowers::class,
                    $requestData
                );

                return ResponseHelper::success_response(
                    'Restaurant followed successfully',
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

    public function near_you(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules(
                $request,
                'near_you'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $distance = 1;

                    $haversine =
                        "(
                6371 * acos(
                    cos(radians(" .
                        $request->latitude .
                        "))
                    * cos(radians(`latitude`))
                    * cos(radians(`longitude`) - radians(" .
                        $request->longitude .
                        "))
                    + sin(radians(" .
                        $request->latitude .
                        ")) * sin(radians(`latitude`))
                )
            )";

                    $restaurant = Resturant::select('*')
                        ->selectRaw("$haversine AS distance")
                        ->having('distance', '<=', $distance)
                        ->orderby('distance', 'desc')
                        ->get();

                    return ResponseHelper::success_response(
                        'All restaurant near you fetched successfully',
                        $restaurant
                    );

                    //  $id = Auth::id();
                } catch (Exception $e) {
                    return ResponseHelper::error_response(
                        'Server Error',
                        $e->getMessage(),
                        401
                    );
                }
            } else {
                $errors = json_decode($validate->errors());
                $props = ['latitude', 'longitude'];
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
        $id = Auth::id();
    }

    public function index()
    {
        $restaurant = DBHelpers::data_with_paginate(
            Resturant::class,
            ['owner'],
            40
        );

        return ResponseHelper::success_response(
            'All restaurant fetched successfully',
            $restaurant
        );
    }
}
