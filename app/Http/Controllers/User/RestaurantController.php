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
use App\Models\SavedRestaurant;
use App\Models\RestaurantReviews;

class RestaurantController extends Controller
{
    //

    //// view restaurant ///
    public function banner()
    {
        $restaurants = Resturant::query()
            ->select('banner')
            ->paginate(20);

        return ResponseHelper::success_response(
            'Restaurant banners',
            $restaurants
        );
    }

    public function top_rated()
    {
        $top_rated = DBHelpers::top_column(Resturant::class, 'total_rating', 3);

        if (count($top_rated) > 0) {
            $uid = Auth::id();
            $following_data = RestaurantFollowers::where([
                'uid' => $uid,
            ])
                ->pluck('restaurant_id')
                ->toArray();

            $restaurant_data = $top_rated;

            foreach ($restaurant_data as $value) {
                if (in_array($value->id, $following_data)) {
                    $value->following = true;
                } else {
                    $value->following = false;
                }
            }
        }

        return ResponseHelper::success_response(
            'Top rated restaurant',
            $top_rated
        );
    }

    public function search_by_name(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules($request, 'search');

            if (!$validate->fails() && $validate->validated()) {
                $current = DBHelpers::query_like_with_filter(
                    Resturant::class,
                    'name',
                    $request->search,
                    ['menu_pic', 'seat_type', 'review']
                );

                if (count($current) > 0) {
                    $uid = Auth::id();
                    $following_data = RestaurantFollowers::where([
                        'uid' => $uid,
                    ])
                        ->pluck('restaurant_id')
                        ->toArray();

                    $restaurant_data = $current;

                    foreach ($restaurant_data as $value) {
                        if (in_array($value->id, $following_data)) {
                            $value->following = true;
                        } else {
                            $value->following = false;
                        }
                    }
                }

                return ResponseHelper::success_response(
                    'Search restaurant by name',
                    $current
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['name'];
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

    public function state_filter(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules(
                $request,
                'state_filter'
            );

            if (!$validate->fails() && $validate->validated()) {
                $distance = 100;

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
                    ->where(['state' => $request->state])
                    ->with(['menu_pic', 'seat_type', 'review'])
                    ->orderby('distance', 'desc')
                    ->get();

                if (count($restaurant) > 0) {
                    $uid = Auth::id();
                    $following_data = RestaurantFollowers::where([
                        'uid' => $uid,
                    ])
                        ->pluck('restaurant_id')
                        ->toArray();

                    $restaurant_data = $restaurant;

                    foreach ($restaurant_data as $value) {
                        if (in_array($value->id, $following_data)) {
                            $value->following = true;
                        } else {
                            $value->following = false;
                        }
                    }
                }

                return ResponseHelper::success_response(
                    'Restaurant by state',
                    $restaurant
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['state'];
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

    public function saved(Request $request)
    {
        $uid = Auth::id();

        $restaurant = DBHelpers::data_with_where_paginate(
            SavedRestaurant::class,
            ['uid' => $uid],
            ['restaurant'],
            40
        );

        if (count($restaurant->items()) > 0) {
            $uid = Auth::id();
            $following_data = RestaurantFollowers::where([
                'uid' => $uid,
            ])
                ->pluck('restaurant_id')
                ->toArray();

            $restaurant_data = $restaurant->items();

            foreach ($restaurant_data as $value) {
                if (in_array($value->id, $following_data)) {
                    $value->following = true;
                } else {
                    $value->following = false;
                }
            }
        }

        return ResponseHelper::success_response(
            'All saved restaurant fetched successfully',
            $restaurant
        );
    }

    public function followed(Request $request)
    {
        $uid = Auth::id();

        $restaurant = DBHelpers::data_with_where_paginate(
            RestaurantFollowers::class,
            ['uid' => $uid],
            ['restaurant'],
            40
        );

        if (count($restaurant->items()) > 0) {
            $uid = Auth::id();
            $following_data = RestaurantFollowers::where([
                'uid' => $uid,
            ])
                ->pluck('restaurant_id')
                ->toArray();

            $restaurant_data = $restaurant->items();

            foreach ($restaurant_data as $value) {
                if (in_array($value->id, $following_data)) {
                    $value->following = true;
                } else {
                    $value->following = false;
                }
            }
        }

        return ResponseHelper::success_response(
            'All followed restaurant fetched successfully',
            $restaurant
        );
    }

    //// view restaurant ///
    public function view(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules($request, 'view');

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

                $uid = Auth::id();

                $following_data = RestaurantFollowers::where([
                    'uid' => $uid,
                ])
                    ->pluck('restaurant_id')
                    ->toArray();

                if (in_array($current->id, $following_data)) {
                    $current->following = true;
                } else {
                    $current->following = false;
                }

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

    //// Rate restaurant ////
    public function rate(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules($request, 'rate');

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

                $requestData = $request->all();
                $requestData['uid'] = Auth::id();

                DBHelpers::create_query(RestaurantReviews::class, $requestData);

                $cureent_restuarant = DBHelpers::query_filter_first(
                    Resturant::class,
                    ['id' => $requestData['restaurant_id']]
                );
                $total_rating = $cureent_restuarant->total_rating;
                $user_rating = $requestData['star_rating'];
                $current_rating = intval($total_rating) + intval($user_rating);

                DBHelpers::update_query_v3(
                    Resturant::class,
                    ['total_rating' => $current_rating],
                    ['id' => $requestData['restaurant_id']]
                );

                return ResponseHelper::success_response(
                    'Restaurant review saved successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['restaurant_id', 'star_rating', 'comment'];
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

    //// unsave restaurant ///
    public function unsave(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules($request, 'unsave');

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                // return $uid = Auth::user();

                /// $user = auth('web-api')->user();

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
                    DBHelpers::exists(SavedRestaurant::class, [
                        'restaurant_id' => $request->restaurant_id,
                        'uid' => $uid,
                    ])
                ) {
                    DBHelpers::delete_query_multi(SavedRestaurant::class, [
                        'restaurant_id' => $request->restaurant_id,
                        'uid' => $uid,
                    ]);
                }

                return ResponseHelper::success_response(
                    'Restaurant unsaved successfully',
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

    //// Save restaurant ////
    public function save(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules($request, 'save');

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
                    DBHelpers::exists(SavedRestaurant::class, [
                        'restaurant_id' => $request->restaurant_id,
                        'uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Restaurant saved already',
                        $validate->errors(),
                        401
                    );
                }

                $requestData = $request->all();
                $requestData['uid'] = Auth::id();

                DBHelpers::create_query(SavedRestaurant::class, $requestData);

                return ResponseHelper::success_response(
                    'Restaurant saved successfully',
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

    //// unfollow restaurant ///
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
                        'restaurant_id' => $request->restaurant_id,
                        'uid' => $uid,
                    ])
                ) {
                    DBHelpers::delete_query_multi(RestaurantFollowers::class, [
                        'restaurant_id' => $request->restaurant_id,
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

    //// follow restaurant ///
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
                        'restaurant_id' => $request->restaurant_id,
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

    //// restaurant near you///
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

                    if (count($restaurant) > 0) {
                        $uid = Auth::id();
                        $following_data = RestaurantFollowers::where([
                            'uid' => $uid,
                        ])
                            ->pluck('restaurant_id')
                            ->toArray();

                        $restaurant_data = $restaurant;

                        foreach ($restaurant_data as $value) {
                            if (in_array($value->id, $following_data)) {
                                $value->following = true;
                            } else {
                                $value->following = false;
                            }
                        }
                    }

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

        if (count($restaurant->items()) > 0) {
            $uid = Auth::id();
            $following_data = RestaurantFollowers::where([
                'uid' => $uid,
            ])
                ->pluck('restaurant_id')
                ->toArray();
            $restaurant_data = $restaurant->items();
            foreach ($restaurant_data as $value) {
                if (in_array($value->id, $following_data)) {
                    $value->following = true;
                } else {
                    $value->following = false;
                }
            }
        }

        return ResponseHelper::success_response(
            'All restaurant fetched successfully',
            $restaurant
        );
    }
}
