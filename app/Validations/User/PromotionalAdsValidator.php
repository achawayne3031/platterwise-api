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

class PromotionalAdsValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [

            'get_promotional_ad' => [
                'promotional_id' => 'required|integer',
            ],

            'search' => [
                'search' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
