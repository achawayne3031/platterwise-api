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

class RestaurantTeamValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'register' => [
                'full_name' => 'required',
                'email' => 'required|email|unique:restaurant_team',
                'restaurant_id' => 'required|integer',
            ],
            'reservation' => [
                'reservation_id' => 'required|integer',
            ],

            'check_in' => [
                'reservation_id' => 'required|integer',
                'restaurant_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
