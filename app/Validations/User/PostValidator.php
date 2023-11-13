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

class PostValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'user_id' => 'required|integer',
                'content_post' => 'required',
                'content_type' => 'required',
                'contentUrl' => 'required',
            ],
            'reservation' => [
                'reservation_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
