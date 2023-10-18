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

class SavedRestaurantValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'save' => [
                'restaurant_id' => 'required|integer',
            ],
            'unsave' => [
                'restaurant_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
