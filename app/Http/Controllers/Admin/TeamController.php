<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Validations\Admin\RestaurantTeamValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\RestaurantTeam;
use App\Helpers\DBHelpers;
use App\Helpers\Func;

class TeamController extends Controller
{
    //

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantTeamValidator::validate_rules(
                $request,
                'register'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $gen_password = Func::generate_reference(10, 'numeric');
                    $data = [
                        'full_name' => $request->full_name,
                        'email' => $request->email,
                        'password' => bcrypt($gen_password),
                        'restaurant_id' => $request->restaurant_id,
                    ];

                    $register = DBHelpers::create_query(
                        RestaurantTeam::class,
                        $data
                    );

                    if ($register) {
                        $mailData = [
                            'password' => $gen_password,
                        ];

                        \Mail::to($request->email)->send(
                            new \App\Mail\TeamRegisterMail($mailData)
                        );

                        return ResponseHelper::success_response(
                            'Registration was Successful',
                            null,
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'Registration failed, Database insertion issues',
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
                $props = ['full_name', 'email', 'restaurant_id'];
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
