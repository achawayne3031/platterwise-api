<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\DBHelpers;
use App\Helpers\ResponseHelper;
use App\Models\Resturant;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantImages;
use App\Models\RestaurantReviews;
use App\Models\Reservation;
use App\Models\Transactions;
use App\Models\UserPosts;

use App\Models\PostComments;

use App\Models\LikedPost;

use App\Validations\SuperAdmin\UserListValidator;
use App\Validations\ErrorValidation;

use App\Models\UserFollowers;
use App\Models\RestaurantFollowers;
use App\Models\SavedRestaurant;

use App\Models\User\AppUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //

    /////  is_suspened

    public function user_post_activities(Request $request, $user)
    {
        if ($request->isMethod('get')) {
            if (
                !DBHelpers::exists(AppUser::class, [
                    'id' => $user,
                ])
            ) {
                return ResponseHelper::error_response(
                    'User not found',
                    null,
                    401
                );
            }

            $posts = DBHelpers::data_with_where_paginate(
                UserPosts::class,
                [
                    'user_id' => $user,
                ],
                ['user']
            );

            return ResponseHelper::success_response(
                'User post activities fetched successfully',
                $posts
            );
        } else {
            return ResponseHelper::error_response(
                'HTTP Request not allowed',
                '',
                404
            );
        }
    }

    public function user_reservation_activities(Request $request, $user)
    {
        if ($request->isMethod('get')) {
            if (
                !DBHelpers::exists(AppUser::class, [
                    'id' => $user,
                ])
            ) {
                return ResponseHelper::error_response(
                    'User not found',
                    null,
                    401
                );
            }

            $reservation = DBHelpers::data_with_where_paginate(
                Reservation::class,
                ['uid' => $user],
                ['restaurant', 'reservation_bill']
            );

            return ResponseHelper::success_response(
                'User reservation activities fetched successfully',
                $reservation
            );
        } else {
            return ResponseHelper::error_response(
                'HTTP Request not allowed',
                '',
                404
            );
        }
    }

    public function remove_user_post(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserListValidator::validate_rules(
                $request,
                'remove_user_post'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(UserPosts::class, [
                        'user_id' => $request->user_id,
                        'id' => $request->post_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not found',
                        null,
                        401
                    );
                }

                DBHelpers::delete_query_multi(UserPosts::class, [
                    'user_id' => $request->user_id,
                    'id' => $request->post_id,
                ]);

                DBHelpers::delete_query_multi(PostComments::class, [
                    'post_id' => $request->post_id,
                ]);

                DBHelpers::delete_query_multi(LikedPost::class, [
                    'post_id' => $request->post_id,
                ]);

                return ResponseHelper::success_response(
                    'User post deleted successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user_id', 'post_id'];
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

    public function activate_suspended_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserListValidator::validate_rules(
                $request,
                'activate_suspended_user'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                $update = DBHelpers::update_query_v3(
                    AppUser::class,
                    ['is_suspended' => 0],
                    ['id' => $request->user_id]
                );

                if (!$update) {
                    return ResponseHelper::error_response(
                        'Update failed, Database insertion issues',
                        null,
                        401
                    );
                }

                return ResponseHelper::success_response(
                    'Suspended user activated successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user_id'];
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

    public function suspend_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserListValidator::validate_rules(
                $request,
                'suspend_user'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                $update = DBHelpers::update_query_v3(
                    AppUser::class,
                    ['is_suspended' => 1],
                    ['id' => $request->user_id]
                );

                if (!$update) {
                    return ResponseHelper::error_response(
                        'Update failed, Database insertion issues',
                        null,
                        401
                    );
                }

                return ResponseHelper::success_response(
                    'User suspended successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user_id'];
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

    public function view_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserListValidator::validate_rules(
                $request,
                'view_user'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                $profile = DBHelpers::query_filter_first(AppUser::class, [
                    'id' => $request->user_id,
                ]);

                $profile->total_posts = DBHelpers::count(UserPosts::class, [
                    'user_id' => $request->user_id,
                ]);

                $reservation = DBHelpers::with_where_query_filter(
                    Reservation::class,
                    ['restaurant'],
                    ['uid' => $request->user_id]
                );

                $followed_restaurant = DBHelpers::with_where_query_filter(
                    RestaurantFollowers::class,
                    ['restaurant'],
                    ['uid' => $request->user_id]
                );

                $saved_restuarant = DBHelpers::with_where_query_filter(
                    SavedRestaurant::class,
                    ['restaurant'],
                    ['uid' => $request->user_id]
                );

                $res_data = [
                    'profile' => $profile,
                    'reservations' => $reservation,
                    'saved_restuarant' => $saved_restuarant,
                    'followed_restaurant' => $followed_restaurant,
                ];

                return ResponseHelper::success_response(
                    'View user fetched successfully',
                    $res_data
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user_id'];
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

    public function delete_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = UserListValidator::validate_rules(
                $request,
                'delete_user'
            );

            if (!$validate->fails() && $validate->validated()) {
                if (
                    !DBHelpers::exists(AppUser::class, [
                        'id' => $request->user_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'User not found',
                        null,
                        401
                    );
                }

                DBHelpers::delete_query_multi(AppUser::class, [
                    'id' => $request->user_id,
                ]);

                return ResponseHelper::success_response(
                    'User deleted successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['user_id'];
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

    public function index(Request $request)
    {
        $users = DBHelpers::with_take_query(AppUser::class, [], 10);

        foreach ($users as $value) {
            # code...

            $value->total_posts = UserPosts::where([
                'user_id' => $value->id,
            ])->count();
            $value->completed_reservation = Reservation::where([
                'uid' => $value->id,
                'status' => 4,
            ])->count();
        }

        $data = [
            'total_users' => AppUser::count(),
            'total_posts' => UserPosts::count(),
            'total_active_users' => AppUser::active()->count(),
            'total_inactive_users' => AppUser::inactive()->count(),
            'user_list' => $users,
        ];

        return ResponseHelper::success_response(
            'User list analysis data fetched was successfully',
            $data
        );
    }

    public function user_list(Request $request)
    {
        $users = DBHelpers::data_paginate(AppUser::class, 30);
        $user_data = $users->items();

        foreach ($user_data as $value) {
            # code...

            $value->total_posts = UserPosts::where([
                'user_id' => $value->id,
            ])->count();
            $value->completed_reservation = Reservation::where([
                'uid' => $value->id,
                'status' => 4,
            ])->count();
        }

        return ResponseHelper::success_response(
            'User list data fetched was successfully',
            $users
        );
    }
}
