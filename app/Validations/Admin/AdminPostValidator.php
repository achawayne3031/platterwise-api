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

class AdminPostValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'create' => [
                'content_post' => 'required',
                'content_type' => 'required',
                // 'contentUrl' => 'required',
            ],
            'reservation' => [
                'reservation_id' => 'required|integer',
            ],

            'like' => [
                'post_id' => 'required|integer',
            ],
            'unlike' => [
                'post_id' => 'required|integer',
            ],

            'delete' => [
                'post_id' => 'required|integer',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
