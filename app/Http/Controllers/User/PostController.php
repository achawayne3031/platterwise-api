<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\PostValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\UserPosts;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

use Illuminate\Support\Facades\Auth;
use App\Services\Paystack;

class PostController extends Controller
{
    //

    public function get_my_posts(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $requestData = $request->all();
                $uid = Auth::id();
                $owner = Auth::user();

                $data = DBHelpers::data_with_where_paginate(
                    UserPosts::class,
                    ['user_id' => $uid],
                    ['user']
                );

                return ResponseHelper::success_response(
                    'Get my posts was successful',
                    $data
                );
            } catch (Exception $e) {
                return ResponseHelper::error_response(
                    'Server Error',
                    $e->getMessage(),
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

    public function get_all_posts(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $requestData = $request->all();
                $uid = Auth::id();
                $owner = Auth::user();

                $data = DBHelpers::data_with_paginate(UserPosts::class, [
                    'user',
                ]);

                return ResponseHelper::success_response(
                    'Get posts was successful',
                    $data
                );
            } catch (Exception $e) {
                return ResponseHelper::error_response(
                    'Server Error',
                    $e->getMessage(),
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
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $requestData = $request->all();
                    $requestData['user_id'] = Auth::id();
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
