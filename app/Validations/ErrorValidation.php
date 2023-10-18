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

class ErrorValidation
{
    public static function arrange_error($data, $props)
    {
        $res_data = [];
        global $error_data;
        $error_data = $data;

        foreach ($props as $value) {
            if (isset($error_data->$value)) {
                $curr = $error_data->$value;
                foreach ($curr as $value) {
                    array_push($res_data, $value);
                }
            }
        }

        return $res_data;
    }

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'login' => [
                'email' => 'required',
                'password' => 'required',
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
