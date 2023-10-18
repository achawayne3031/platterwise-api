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

    public static function school_session_builder($last = 10)
    {
        $yearly = [];
        $current_year = date('Y');
        $start_year = $current_year - $last;
        $next_year = $current_year + 1;
        $next_session = $current_year . '/' . $next_year;

        for ($i = 0; $i < $last; $i++) {
            $add = $i + 1;
            $sessions_years = $start_year + $i . '/' . $start_year + $add;
            array_push($yearly, $sessions_years);
        }

        array_push($yearly, $next_session);

        return $yearly;
    }

    public static function generate_admission_code($serial_no)
    {
        return isset($serial_no) && !empty($serial_no)
            ? '000' . $serial_no
            : false;
    }

    public static function generate_school_admission_no($serial_no)
    {
        $current_session = DB::table('school_session_data')
            ->where(['is_active' => 1])
            ->get()
            ->first();
        $school_data = DB::table('school_settings')
            ->get()
            ->first();

        if (!$current_session && !$school_data) {
            return false;
        }

        $school_prefix = $school_data->school_prefix;
        $current_session_year = $current_session->year;

        return $school_prefix .
            '/' .
            $current_session_year .
            '/' .
            Func::generate_admission_code($serial_no);
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
