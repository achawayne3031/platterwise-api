<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\SavedRestaurant;
use App\Models\Resturant;
use App\Models\Reservation;
use App\Models\RestaurantFollowers;
use App\Models\UserFollowers;

use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;
use App\Models\User\AppUser;
use App\Validations\UserAuthValidator;
use App\Validations\User\UserFollowerValidator;

class UserController extends Controller
{
    //

    //// unfollow user ///
    public function unfollow(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserFollowerValidator::validate_rules(
                $request,
                'unfollow'
            );

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                if (
                    !DBHelpers::exists(UserFollowers::class, [
                        'follower' => $uid,
                        'user' => $request->user,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'You are not following this user',
                        null,
                        401
                    );
                }

                $delete_data = [
                    'user' => $request->user,
                    'follower' => Auth::id(),
                ];

                DBHelpers::delete_query_multi(
                    UserFollowers::class,
                    $delete_data
                );

                $user_owner = AppUser::find($request->user);
                // increment the value of the `follower` column by 1
                $user_owner->decrement('followers');

                $user_follower = AppUser::find(Auth::id());
                // increment the value of the `following` column by 1
                $user_follower->decrement('following');

                return ResponseHelper::success_response(
                    'User unfollowed successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user'];
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

    //// follow user ///
    public function follow(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserFollowerValidator::validate_rules(
                $request,
                'follow'
            );

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                if (
                    DBHelpers::exists(UserFollowers::class, [
                        'follower' => $uid,
                        'user' => $request->user,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User followed already',
                        null,
                        401
                    );
                }

                $insert = [
                    'user' => $request->user,
                    'follower' => Auth::id(),
                ];

                DBHelpers::create_query(UserFollowers::class, $insert);

                $user_owner = AppUser::find($request->user);
                // increment the value of the `follower` column by 1
                $user_owner->increment('followers');

                $user_follower = AppUser::find(Auth::id());
                // increment the value of the `following` column by 1
                $user_follower->increment('following');

                return ResponseHelper::success_response(
                    'User followed successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user'];
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

    public function search_by_name(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserAuthValidator::validate_rules(
                $request,
                'search_name'
            );

            if (!$validate->fails() && $validate->validated()) {
                $current = DBHelpers::query_like_with_filter(
                    AppUser::class,
                    'full_name',
                    $request->name,
                    ['reservation']
                );

                return ResponseHelper::success_response(
                    'Search user by name',
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

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserAuthValidator::validate_rules($request, 'edit');

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $uid = Auth::id();

                    $user_data = DBHelpers::query_filter_first(AppUser::class, [
                        'id' => $uid,
                    ]);

                    // return $user_data;

                    $data = [
                        'full_name' => isset($request->full_name)
                            ? $request->full_name
                            : $user_data->full_name,
                        'phone' => isset($request->phone)
                            ? $request->phone
                            : $user_data->phone,
                        'location' => isset($request->location)
                            ? $request->location
                            : $user_data->location,
                        'username' => isset($request->username)
                            ? $request->username
                            : $user_data->username,
                        'bio' => isset($request->bio)
                            ? $request->bio
                            : $user_data->bio,
                        'profileUrl' => isset($request->profileUrl)
                            ? $request->profileUrl
                            : $user_data->profileUrl,
                    ];

                    $update = DBHelpers::update_query_v3(
                        AppUser::class,
                        $data,
                        ['id' => $uid]
                    );

                    if ($update) {
                        return ResponseHelper::success_response(
                            'User update was successful',
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'Update failed, Database insertion issues',
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
                    'full_name',
                    'phone',
                    'email',
                    'password',
                    'username',
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

    public function profile()
    {
        $uid = Auth::id();

        $profile = DBHelpers::query_filter_first(AppUser::class, [
            'id' => $uid,
        ]);

        $reservation = DBHelpers::with_where_query_filter(
            Reservation::class,
            ['restaurant'],
            ['uid' => $uid]
        );

        $followed_restaurant = DBHelpers::with_where_query_filter(
            RestaurantFollowers::class,
            ['restaurant'],
            ['uid' => $uid]
        );

        $saved_restuarant = DBHelpers::with_where_query_filter(
            SavedRestaurant::class,
            ['restaurant'],
            ['uid' => $uid]
        );

        $res_data = [
            'profile' => $profile,
            'reservations' => $reservation,
            'saved_restuarant' => $saved_restuarant,
            'followed_restaurant' => $followed_restaurant,
        ];

        return ResponseHelper::success_response(
            'User profile fetched successfully',
            $res_data
        );
    }
}
