<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Validations\SuperAdmin\PromotionalAdsValidator;
use App\Validations\ErrorValidation;
use App\Helpers\ResponseHelper;
use App\Models\PromotionalAds;
use App\Helpers\DBHelpers;
use App\Helpers\Func;



class PromtionalAdsController extends Controller
{
    //

    public function get_promotional_ad(Request $request)
    {
        if ($request->isMethod('post')) {

            $validate = PromotionalAdsValidator::validate_rules($request, 'get_promotional_ad');

            if (!$validate->fails() && $validate->validated()) {
                try {

                    $data = PromotionalAds::where(['id' => $request->promotional_id])
                        ->get()
                        ->first();

                    $post = PromotionalAds::find($request->promotional_id);
                    // increment the value of the `follower` column by 1
                    $post->increment('total_views');

                  

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
                $props = ['promotional_ad_id'];

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



    public function get_all_promotional_ads(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
             

                $data = DBHelpers::data_paginate(PromotionalAds::class);

                return ResponseHelper::success_response(
                    'Get promotional ads was successful',
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
            $validate = PromotionalAdsValidator::validate_rules($request, 'create');

            if (!$validate->fails() && $validate->validated()) {
                try {

                  

                    $register = DBHelpers::create_query(
                        PromotionalAds::class,
                        $request->all()
                    );

                    if ($register) {
                        return ResponseHelper::success_response(
                            'Promotional Ads created was successful',
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
                    'title',
                    'message',
                    'from_duration',
                    'to_duration',
                    'img_url',

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
