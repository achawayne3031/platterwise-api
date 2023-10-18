<?php

namespace App\Services;

use App\Models\StmpSettings;
use App\Helpers\ResponseHelper;
use Mail;
use App\Mail\DefaultMessagingMail;

class SmtpService
{
    public static function setMailConfig()
    {
        //Get the data from settings table
        $settings = StmpSettings::query()
            ->get()
            ->first();

        if ($settings) {
            //Set the data in an array variable from settings table
            $mailConfig = [
                'transport' => 'smtp',
                'host' => $settings->hostname,
                'port' => $settings->port,
                'encryption' => $settings->protocol,
                'username' => $settings->username,
                'password' => $settings->password,
                'from' => $settings->from_address,
                'timeout' => null,
            ];

            $fromConfig = [
                'address' => $settings->from_address,
                'name' => 'my name',
            ];

            //To set configuration values at runtime, pass an array to the config helper
            config([
                'mail.mailers.smtp' => $mailConfig,
                'mail.from' => $fromConfig,
            ]);
        }
    }

    public static function send_smtp_mail($email = null, $data)
    {
        SmtpService::setMailConfig();
        if ($email != null) {
            return Mail::to($email)->send(new DefaultMessagingMail($data));
        }
    }
}

?>
