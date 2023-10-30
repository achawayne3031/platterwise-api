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

class RestaurantValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'reservation_date' => 'required|date',
                'restaurant_id' => 'required|integer',
                'seat_type' => 'required',
                'guest_no' => 'required',
            ],

            'near_you' => [
                'latitude' => 'required',
                'longitude' => 'required',
            ],
            'save' => [
                'restaurant_id' => 'required|integer',
            ],
            'unsave' => [
                'restaurant_id' => 'required|integer',
            ],
            'rate' => [
                'restaurant_id' => 'required|integer',
                'comment' => 'required',
                'star_rating' => 'required|integer',
            ],
            'view' => [
                'restaurant_id' => 'required|integer',
            ],
            'state_filter' => [
                'state' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
            ],
            'search' => [
                'name' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
