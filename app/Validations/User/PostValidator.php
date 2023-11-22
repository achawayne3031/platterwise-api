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

            'get_post' => [
                'post_id' => 'required|integer',
            ],

            'search' => [
                'search' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
