<?php
/**
 *
 *
 * @category Validations
 * @author	Platterwise
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\SuperAdmin;
use App\Helpers\Func;

class PromotionalAdsValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'title' => 'required',
                'message' => 'required',
                'from_duration' => 'required',
                'to_duration' => 'required',
                'img_url' => 'required',
            ],
          

            'get_promotional_ad' => [
                'promotional_id' => 'required|integer',
            ],

            

            'delete' => [
                'post_id' => 'required|integer',
            ],

            'search' => [
                'search' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
