<?php /**
 *
 *
 * @category Validations
 * @author	Platterwise
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\SuperAdmin;
use App\Helpers\Func;

class AuthValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'login' => [
                'email' => 'required',
                'password' => 'required',
            ],
            'validate_email' => [
                'email' => 'required',
            ],
            'reset_password' => [
                'email' => 'required',
                'password' => 'required',
                'token' => 'required',
            ],
            'reset_token' => [
                'email' => 'required',
            ],

            'token' => [
                'token' => 'required',
            ],
            'set_password' => [
                'email' => 'required',
                'password' => 'required',
            ],

            'register' => [
                'full_name' => 'required',
                'email' => 'required|email|unique:super_admin',
                'password' => 'required|min:8',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
