<?php
/**
 *
 *
 * @category Validations
 * @author	Champa
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations;
use App\Helpers\Func;

class UserAuthValidator
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

            'change_password' => [
                'old_password' => 'required',
                'new_password' => 'required|same:password',
            ],


            'register' => [
                'phone' => 'required',
                'full_name' => 'required',
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ],
            'edit' => [
                'full_name' => 'required',
                'username' => 'required',
                'phone' => 'required',
            ],
            'search_name' => [
                'name' => 'required',
            ],

            'other_user' => [
                'user_id' => 'required|integer',
            ],

            'validate_email' => [
                'email' => 'required',
            ],
            'reset_password' => [
                'email' => 'required',
                'password' => 'required',
                'token' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
