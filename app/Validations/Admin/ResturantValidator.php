<?php
/**
 *
 *
 * @category Validations
 * @author	Platterwise
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\Admin;
use App\Helpers\Func;

class ResturantValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'admin_uid' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'state' => 'required',
                'local_govt' => 'required',
                'landmark' => 'required',
                'cover_pic' => 'required',
                'banner' => 'required',
                'descriptions' => 'required',
                'menu_pic' => 'required',
                'opening_hour' => 'required',
                'closing_hour' => 'required',
                'website' => 'required',
                'seat_type' => 'required',
                'social_handle' => 'required',
                'kyc' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ],

            'token' => [
                'token' => 'required',
            ],
            'set_password' => [
                'email' => 'required',
                'password' => 'required',
            ],

            'reset_password' => [
                'password' => 'required',
                'otp' => 'required',
                'verify_password' => 'required|same:password',
            ],

            'register' => [
                'phone' => 'required',
                'full_name' => 'required',
                'email' => 'required|email|unique:admin_users',
                'username' => 'required|unique:admin_users',
                'password' => 'required|min:8',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
