<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validations\User\PostCommentValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\UserPosts;
use App\Models\PostComments;
use App\Models\LikedPost;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

use Illuminate\Support\Facades\Auth;
use App\Services\Paystack;

class CommentController extends Controller
{
    //

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = PostCommentValidator::validate_rules(
                $request,
                'create'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $requestData = $request->all();
                    $requestData['user'] = Auth::id();

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

                    $register = DBHelpers::create_query(
                        PostComments::class,
                        $requestData
                    );

                    $post = UserPosts::find($request->post_id);
                    // increment the value of the `follower` column by 1
                    $post->increment('total_comments');

                    if ($register) {
                        return ResponseHelper::success_response(
                            'Comment created was successful',
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
                $props = ['post_id', 'comment'];

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
