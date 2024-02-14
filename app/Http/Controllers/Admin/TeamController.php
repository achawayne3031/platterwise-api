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
use App\Models\Resturant;

class TeamController extends Controller
{
    //

    public function all_team(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantTeamValidator::validate_rules(
                $request,
                'all_team'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    $user = auth('web-api')->user();
                    $uid = $user->id;

                    if (
                        !DBHelpers::exists(Resturant::class, [
                            'admin_uid' => $uid,
                            'id' => $request->restaurant_id,
                        ])
                    ) {
                        return ResponseHelper::error_response(
                            'Restaurant not found on your collection',
                            null,
                            401
                        );
                    }

                    $all_team = DBHelpers::data_where_paginate(
                        RestaurantTeam::class,
                        ['restaurant_id' => $request->restaurant_id],
                        40
                    );

                    return ResponseHelper::success_response(
                        'All restaurant team',
                        $all_team
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

    public function remove_team(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantTeamValidator::validate_rules(
                $request,
                'remove_team'
            );

            if (!$validate->fails() && $validate->validated()) {
                try {
                    if (
                        DBHelpers::exists(RestaurantTeam::class, [
                            'id' => $request->id,
                            'restaurant_id' => $request->restaurant_id,
                        ])
                    ) {
                        DBHelpers::delete_query_multi(RestaurantTeam::class, [
                            'id' => $request->id,
                            'restaurant_id' => $request->restaurant_id,
                        ]);

                        return ResponseHelper::success_response(
                            'Team member deleted successful',
                            null,
                            null
                        );
                    } else {
                        return ResponseHelper::error_response(
                            'User not found',
                            'Team member not found',
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
                $props = ['id', 'restaurant_id'];
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

                        try {
                            //code...

                            \Mail::to($request->email)->send(
                                new \App\Mail\TeamRegisterMail($mailData)
                            );
                        } catch (\Throwable $th) {
                            return ResponseHelper::error_response(
                                'Registration failed, Database insertion issues',
                                $th,
                                401
                            );

                            //throw $th;
                        }

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
