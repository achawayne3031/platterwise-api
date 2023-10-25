<?php

/**
 *
 *
 * @package
 * @author	School Mgt
 * @copyright
 * @version	1.0.0
 */

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Func
{
    public static function run_validation(Request $request, array $input)
    {
        return Validator::make($request->all(), $input);
    }

    public static function generate_reference($length = 4, $mixture = 'all')
    {
        $lowercase_alpha = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase_alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        switch ($mixture) {
            // only lowercase mixture
            case 'lower':
                $permitted_chars = $lowercase_alpha;
                break;
            // only uppercase mixture
            case 'upper':
                $permitted_chars = $uppercase_alpha;
                break;
            // only numeric mixture
            case 'numeric':
                $permitted_chars = $numbers;
                break;
            // only alphabets mixture
            case 'alpha':
                $permitted_chars = $lowercase_alpha . $uppercase_alpha;
                break;
            // numeric lowercase mixture
            case 'lower-numeric':
                $permitted_chars = $numbers . $lowercase_alpha;
                break;
            // numeric uppercase mixture
            case 'upper-numeric':
                $permitted_chars = $numbers . $uppercase_alpha;
                break;
            // all mixtures available
            default:
                $permitted_chars =
                    $numbers . $lowercase_alpha . $uppercase_alpha;
                break;
        }

        // Output: $prefix-g6swmAP8X5VG4jCi-$suffix
        return substr(str_shuffle($permitted_chars), 0, $length);
    }

    public static function full_phone_number($number)
    {
        if ($number !== '' && !empty($number)) {
            $first_number = substr($number, 0, 1);
            if ($first_number !== '0') {
                return '0' . $number;
            }
        }
        return $number;
    }
}
