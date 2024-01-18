<?php
/**
 *
 *
 * @category Validations
 * @author	Champa
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\Team;
use App\Helpers\Func;

class ReservationValidators
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'reservations' => [
                'restaurant_id' => 'required|integer',
            ],
            'check_in' => [
                'reservation_id' => 'required|integer',
                'restaurant_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
