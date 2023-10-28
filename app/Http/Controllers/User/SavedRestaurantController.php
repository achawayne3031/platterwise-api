<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\SavedRestaurantValidator;

use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\SavedRestaurant;
use App\Helpers\DBHelpers;
use Illuminate\Support\Facades\Auth;

class SavedRestaurantController extends Controller
{
    //

    public function save(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = SavedRestaurantValidator::validate_rules(
                $request,
                'save'
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
                    DBHelpers::exists(SavedRestaurant::class, [
                        'id' => $request->restaurant_id,
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
}
