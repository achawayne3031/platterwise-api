<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Validations\Admin\AdminAuthValidator;
use App\Validations\Admin\ResturantValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Resturant;
use App\Models\Admin\RestaurantSeatType;
use App\Models\Admin\RestaurantImages;
use App\Helpers\DBHelpers;

class ResturantController extends Controller
{
    //

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = ResturantValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
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
                    'social_handle' => $request->social_handle,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ];

                $create = DBHelpers::create_query(
                    Resturant::class,
                    $restaurant_data
                );
                $restaurant_id = $create->id;

                if (count($request->seat_type) > 0) {
                    $seat = $request->seat_type;
                    foreach ($seat as $value) {
                        DBHelpers::create_query(RestaurantSeatType::class, [
                            'name' => $value->name,
                            'restaurant_id' => $restaurant_id,
                        ]);
                    }
                }

                if (count($request->menu_picture) > 0) {
                    $image = $request->menu_picture;
                    foreach ($image as $value) {
                        DBHelpers::create_query(RestaurantImages::class, [
                            'image_url' => $value->menu_pic,
                            'restaurant_id' => $restaurant_id,
                        ]);
                    }
                }

                return ResponseHelper::success_response(
                    'Restaurant created successfully',
                    null
                );
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

        $id = Auth::id();

        $create = DBHelpers::create_query(User::class, $user_data);
        $uid = $create->id;
    }
}
