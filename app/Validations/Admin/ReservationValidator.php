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

class ReservationValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'all' => [
                'restaurant_id' => 'required|integer',
            ],
            'cancel' => [
                'reservation_id' => 'required|integer',
                'restaurant_id' => 'required|integer',
            ],
            'approve' => [
                'reservation_id' => 'required|integer',
                'restaurant_id' => 'required|integer',
            ],
            'edit' => [
                'reservation_id' => 'required|integer',
                'restaurant_id' => 'required|integer',
                'reservation_date' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
