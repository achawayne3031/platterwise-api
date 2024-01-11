<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

class TeamRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailMessageData;

    public function __construct($mailMessageData)
    {
        //
        $this->mailMessageData = $mailMessageData;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Platterwise Restaurant Team')
            ->view('admin.RestaurantTeamRegister');
    }
}
