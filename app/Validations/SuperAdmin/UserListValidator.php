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

class UserListValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'delete_user' => [
                'user_id' => 'required|integer',
            ],

            'view_user' => [
                'user_id' => 'required|integer',
            ],

            'suspend_user' => [
                'user_id' => 'required|integer',
            ],

            'activate_suspended_user' => [
                'user_id' => 'required|integer',
            ],

            'remove_user_post' => [
                'user_id' => 'required|integer',
                'post_id' => 'required|integer',
            ],

            'user_reservation_activities' => [
                'user_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
