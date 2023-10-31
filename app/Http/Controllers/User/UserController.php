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
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;
use App\Models\User\AppUser;
use App\Validations\UserAuthValidator;

class UserController extends Controller
{
    //

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

                    $data = [
                        'full_name' =>
                            $request->full_name ?? $user_data->full_name,
                        'phone' => $request->phone ?? $user_data->phone,
                        'location' =>
                            $request->location ?? isset($user_data->location)
                                ? $user_data->location
                                : null,
                        'username' =>
                            $request->username ?? $user_data->username,
                        'bio' =>
                            $request->bio ?? isset($user_data->bio)
                                ? $user_data->bio
                                : null,
                        'profileUrl' =>
                            $request->profileUrl ??
                            isset($user_data->profileUrl)
                                ? $user_data->profileUrl
                                : null,
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
