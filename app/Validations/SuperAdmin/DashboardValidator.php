<?php /**
 *
 *
 * @category Validations
 * @author	Platterwise
 * @copyright Copyright (c) 2022. All right reserved
 * @version	1.0
 */

namespace App\Validations\SuperAdmin;
use App\Helpers\Func;

class DashboardValidator
{
    protected static $validation_rules = [];

    public static function validate_rules($request, string $arg)
    {
        self::$validation_rules = [
            'reservation_report' => [
                'month' => 'required',
            ],
        ];

        return Func::run_validation($request, self::$validation_rules[$arg]);
    }
}
