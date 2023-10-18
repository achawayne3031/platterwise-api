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

class AdminAuthValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'login' => [
                'email' => 'required',
                'password' => 'required',
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
