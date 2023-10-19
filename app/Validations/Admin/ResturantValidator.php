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

class ResturantValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'admin_uid' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:resturant',
                'phone' => 'required',
                'address' => 'required',
                'state' => 'required',
                'cover_pic' => 'required',
                'banner' => 'required',
                'descriptions' => 'required',
                'menu_picture' => 'required|array',
                'opening_hour' => 'required',
                'closing_hour' => 'required',
                'website' => 'required',
                'seat_type' => 'required|array',
                'social_handle' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'days' => 'required',
            ],

            'reviews' => [
                'restaurant_id' => 'required',
            ],
            'menu' => [
                'restaurant_id' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
