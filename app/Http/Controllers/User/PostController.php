<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\PostValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\UserPosts;
use App\Models\LikedPost;

use App\Helpers\DBHelpers;
use App\Helpers\Func;

use Illuminate\Support\Facades\Auth;
use App\Services\Paystack;

class PostController extends Controller
{
    //////

    public function top_commented()
    {
        $uid = Auth::id();

        $data = UserPosts::query()
            ->with(['user', 'admin'])
            ->with([
                'comments' => function ($query) {
                    $query->with('user');
                },
            ])
            ->orderBy('total_comments', 'DESC')
            ->paginate(30);

        $mylikedpost = LikedPost::where([
            'uid' => $uid,
        ])
            ->pluck('post_id')
            ->toArray();

        $post_data = $data->items();

        foreach ($post_data as $value) {
            if (in_array($value->id, $mylikedpost)) {
                $value->is_liked = true;
            } else {
                $value->is_liked = false;
            }
        }

        return ResponseHelper::success_response('Top commented post', $data);
    }

    public function top_liked()
    {
        $uid = Auth::id();

        $data = UserPosts::query()
            ->with(['user', 'admin'])
            ->with([
                'comments' => function ($query) {
                    $query->with('user');
                },
            ])
            ->orderBy('total_likes', 'DESC')
            ->paginate(30);

        $mylikedpost = LikedPost::where([
            'uid' => $uid,
        ])
            ->pluck('post_id')
            ->toArray();

        $post_data = $data->items();

        foreach ($post_data as $value) {
            if (in_array($value->id, $mylikedpost)) {
                $value->is_liked = true;
            } else {
                $value->is_liked = false;
            }
        }

        return ResponseHelper::success_response('Top liked post', $data);
    }

    public function search_post(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'search');

            $uid = Auth::id();

            if (!$validate->fails() && $validate->validated()) {
                $data = UserPosts::where(
                    'content_post',
                    'LIKE',
                    "%{$request->search}%"
                )
                    ->with([
                        'comments' => function ($query) {
                            $query->with('user');
                        },
                    ])
                    ->with(['user', 'admin'])
                    ->paginate(30);

                $mylikedpost = LikedPost::where([
                    'uid' => $uid,
                ])
                    ->pluck('post_id')
                    ->toArray();

                $post_data = $data->items();

                foreach ($post_data as $value) {
                    if (in_array($value->id, $mylikedpost)) {
                        $value->is_liked = true;
                    } else {
                        $value->is_liked = false;
                    }
                }

                return ResponseHelper::success_response(
                    'Search post by content',
                    $data
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['search'];
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

    public function get_post(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'get_post');

            $uid = Auth::id();

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $data = UserPosts::where(['id' => $request->post_id])
                        ->with(['user', 'admin'])
                        ->with([
                            'comments' => function ($query) {
                                $query->with('user');
                            },
                        ])
                        ->get()
                        ->first();

                    // $mylikedpost = LikedPost::where([
                    //     'uid' => $uid,
                    // ])
                    //     ->pluck('post_id')
                    //     ->toArray();

                    // if (in_array($data->id, $mylikedpost)) {
                    //     $data->is_liked = true;
                    // } else {
                    //     $data->is_liked = false;
                    // }

                    return ResponseHelper::success_response(
                        'Get post was successful',
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
                $errors = json_decode($validate->errors());
                $props = ['post_id'];

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

    //// get my liked posts ///
    public function get_my_liked_posts(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $requestData = $request->all();
                $uid = Auth::id();
                $owner = Auth::user();

                $data = DBHelpers::data_with_where_paginate(
                    LikedPost::class,
                    ['uid' => $uid],
                    ['user', 'post']
                );

                return ResponseHelper::success_response(
                    'Get my liked posts was successful',
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

    //// Delete Post ///
    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'delete');

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                if (
                    !DBHelpers::exists(UserPosts::class, [
                        'id' => $request->post_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not found',
                        $validate->errors(),
                        401
                    );
                }

                if (
                    !DBHelpers::exists(UserPosts::class, [
                        'id' => $request->post_id,
                        'user_id' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not in your collections',
                        $validate->errors(),
                        401
                    );
                }

                if (
                    DBHelpers::exists(UserPosts::class, [
                        'id' => $request->post_id,
                        'user_id' => $uid,
                    ])
                ) {
                    DBHelpers::delete_query_multi(UserPosts::class, [
                        'id' => $request->post_id,
                        'user_id' => $uid,
                    ]);

                    DBHelpers::delete_query_multi(LikedPost::class, [
                        'post_id' => $request->post_id,
                    ]);
                }

                return ResponseHelper::success_response(
                    'Post deleted successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['post_id'];
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

    //// Like Post ///
    public function unlike(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'unlike');

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                if (
                    !DBHelpers::exists(UserPosts::class, [
                        'id' => $request->post_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not found',
                        $validate->errors(),
                        401
                    );
                }

                if (
                    !DBHelpers::exists(LikedPost::class, [
                        'post_id' => $request->post_id,
                        'uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not liked by you',
                        $validate->errors(),
                        401
                    );
                }

                if (
                    DBHelpers::exists(LikedPost::class, [
                        'post_id' => $request->post_id,
                        'uid' => $uid,
                    ])
                ) {
                    DBHelpers::delete_query_multi(LikedPost::class, [
                        'post_id' => $request->post_id,
                        'uid' => $uid,
                    ]);
                }

                $post = UserPosts::find($request->post_id);
                // increment the value of the `follower` column by 1
                $post->decrement('total_likes');

                return ResponseHelper::success_response(
                    'Post unliked successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['post_id'];
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

    //// Like Post ///
    public function like(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostValidator::validate_rules($request, 'like');

            if (!$validate->fails() && $validate->validated()) {
                $uid = Auth::id();

                if (
                    !DBHelpers::exists(UserPosts::class, [
                        'id' => $request->post_id,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post not found',
                        $validate->errors(),
                        401
                    );
                }

                if (
                    DBHelpers::exists(LikedPost::class, [
                        'post_id' => $request->post_id,
                        'uid' => $uid,
                    ])
                ) {
                    return ResponseHelper::error_response(
                        'Post liked already',
                        $validate->errors(),
                        401
                    );
                }

                $requestData = $request->all();
                $requestData['uid'] = Auth::id();

                DBHelpers::create_query(LikedPost::class, $requestData);

                $post = UserPosts::find($request->post_id);
                // increment the value of the `follower` column by 1
                $post->increment('total_likes');

                return ResponseHelper::success_response(
                    'Post Liked successfully',
                    null
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['post_id'];
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

                $mylikedpost = LikedPost::where([
                    'uid' => $uid,
                ])
                    ->pluck('post_id')
                    ->toArray();

                $post_data = $data->items();

                foreach ($post_data as $value) {
                    if (in_array($value->id, $mylikedpost)) {
                        $value->is_liked = true;
                    } else {
                        $value->is_liked = false;
                    }
                }

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
                    'admin',
                ]);

                $mylikedpost = LikedPost::where([
                    'uid' => $uid,
                ])
                    ->pluck('post_id')
                    ->toArray();

                $post_data = $data->items();

                foreach ($post_data as $value) {
                    if (in_array($value->id, $mylikedpost)) {
                        $value->is_liked = true;
                    } else {
                        $value->is_liked = false;
                    }
                }

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
                    $requestData['type'] = 'user';

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
