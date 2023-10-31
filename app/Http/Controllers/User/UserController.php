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

                    // $data = [
                    //     'full_name' => $request->full_name,
                    //     'phone' => $request->phone,
                    //     'email' => $request->email,
                    //     'location' => $request->location,
                    //     'username' => $request->username,
                    //     'bio' => $request->bio ?? null,
                    //     'img_url' => $request->profileUrl ?? null,
                    // ];

                    $update = AppUser::where(['id' => $uid])->update(
                        $request->only([
                            'full_name',
                            'phone',
                            'location',
                            'username',
                            'bio',
                            'profileUrl',
                        ])
                    );

                    // $update = DBHelpers::update_query_v3(
                    //     AppUser::class,
                    //     $data,
                    //     ['id' => $uid]
                    // );

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
