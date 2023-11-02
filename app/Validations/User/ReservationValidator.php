<?php
/**
 *
 *
 * @category Validations
 * @author	Platterwise
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\User;
use App\Helpers\Func;

class ReservationValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'reservation_date' => 'required',
                'restaurant_id' => 'required|integer',
                'seat_type' => 'required',
            ],

            'cancel' => [
                'reservation_id' => 'required|integer',
            ],

            'split_bills' => [
                'reservation_id' => 'required|integer',
                'total_amount' => 'required',
                'guests' => 'required|array',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
