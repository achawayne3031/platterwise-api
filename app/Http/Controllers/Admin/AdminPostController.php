<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\Admin\AdminPostValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\UserPosts;
use App\Models\LikedPost;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

use Illuminate\Support\Facades\Auth;
use App\Services\Paystack;

class AdminPostController extends Controller
{
    //

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = AdminPostValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $user = auth('web-api')->user();
                    $uid = $user->id;

                    $requestData = $request->all();
                    $requestData['admin_uid'] = $uid;
                    $requestData['type'] = 'admin';

                    $owner = Auth::user();

                    $register = DBHelpers::create_query(
                        UserPosts::class,
                        $requestData
                    );

                    if ($register) {
                        return ResponseHelper::success_response(
                            'Post created was successful',
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'Creation failed, Database insertion issues',
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
                    'user_id',
                    'content_post',
                    'content_type',
                    'contentUrl',
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
