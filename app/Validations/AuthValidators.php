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

class AuthValidators
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

            'user_register' => [
                'role' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ],

            'reset_password' => [
                'password' => 'required',
                'otp' => 'required',
                'verify_password' => 'required|same:password',
            ],

            'register' => [
                'full_name' => 'required',
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
