<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendDinnerInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    public $timeout = 7200; // 2 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $guests = $this->details['guests'];

        foreach ($guests as $value) {
            $jobMailData = [
                'owner_name' => $this->details['owner_name'],
                'restaurant' => $this->details['restaurant'],
                'guest_name' => $value->name,
                'location' => $this->details['location'],
                'book_date' => $this->details['book_date'],
                'book_time' => $this->details['book_time'],
            ];

            \Mail::to($value->email)->send(
                new \App\Mail\DiningInvite($jobMailData)
            );
        }
    }
}
