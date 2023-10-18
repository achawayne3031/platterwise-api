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
use App\Services\SmtpService;
use App\Models\EmailTemplate;
use App\Models\SchoolSettings;
use App\Helpers\DBHelpers;

class SendMailHelper
{
    private static $web_url = 'https://school-mgt-v1.netlify.app/auth/set-password/';

    public static function mail_type_selector($type, $email, $data)
    {
        switch ($type) {
            case 'Welcome Email Staff':
                $template = EmailTemplate::where([
                    'name' => 'Welcome Email Staff',
                ])
                    ->get()
                    ->first();

                if ($template) {
                    return SendMailHelper::login_verify_account(
                        $template,
                        $email,
                        $data
                    );
                }

                break;

            default:
                # code...
                break;
        }
    }

    public static function login_verify_account($template, $email, $data)
    {
        $full_name =
            $data['surname'] .
            ' ' .
            $data['first_name'] .
            ' ' .
            $data['other_name'];
        $school_name = '';

        $school_data = DBHelpers::first_data(SchoolSettings::class);
        if ($school_data) {
            $school_name = $school_data->name;
        }

        $password_url =
            'https://school-mgt-v1.netlify.app/auth/set-password/' .
            $data['token'];

        $text = str_replace('@name', $full_name, $template->body);
        $text = str_replace(
            '@set_password_url',
            '<a href="' . $password_url . '"> Click Here </a>',
            $text
        );

        $text = str_replace('@school_name', $school_name, $text);

        $data = [
            'subject' => $template->name,
            'message' => $text,
        ];

        SmtpService::send_smtp_mail($email, $data);
    }
}
