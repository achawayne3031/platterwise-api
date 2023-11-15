<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Paystack;
use App\Models\ReservationSplitBills;
use App\Models\Transactions;

use App\Helpers\DBHelpers;
use App\Helpers\Func;

class SendBillPayment implements ShouldQueue
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

        global $reservation_id;
        global $restaurant_id;
        global $total_amount;

        $guests = $this->details['guests'];
        $restaurant_id = $this->details['restaurant_id'];
        $reservation_id = $this->details['reservation_id'];
        $total_amount = $this->details['total_amount'];

        $set_guest = [];

        foreach ($guests as $value) {
            $payment_ref = Func::generate_reference(20);

            $post_data = [
                'email' => $value['guest_email'],
                'amount' => $value['bill'] * 100,
                'callback_url' =>
                    'https://api2.platterwise.com/verify-payment/' .
                    $payment_ref,
            ];

            $paystack = Paystack::intializeTransaction($post_data);

            if ($paystack->status) {
                $auth_url = $paystack->data->authorization_url;
                $access_code = $paystack->data->access_code;
                $reference = $paystack->data->reference;

                $desc = $value['guest_name'] . ' Payment of  ' . $value['bill'];

                $in_guest = [
                    'guest_email' => $value['guest_email'],
                    'guest_name' => $value['guest_name'],
                    'type' => $value['type'],
                    'bill' => $value['bill'],
                    'payment_url' => $auth_url,
                    'amount_paid' => 0,
                ];

                array_push($set_guest, $in_guest);

                $transaction_data = [
                    'restaurant_id' => $restaurant_id,
                    'reservation_id' => $reservation_id,
                    'email' => $value['guest_email'],
                    'guest_name' => $value['guest_name'],
                    'payment_type' => 'card',
                    'description' => $desc,
                    'ref' => $reference,
                    'amount' => $value['bill'] * 100,
                    'init_extra' => json_encode($paystack->data),
                    'payment_ref' => $payment_ref,
                ];

                $register = DBHelpers::create_query(
                    Transactions::class,
                    $transaction_data
                );

                $jobMailData = [
                    'payment_link' => $auth_url,
                    'restaurant' => $this->details['restuarant'],
                    'restaurant_name' => $this->details['restaurant_name'],
                    'guest_name' => $value['guest_name'],
                    'amount' => $value['bill'],
                ];

                \Mail::to($value['guest_email'])->send(
                    new \App\Mail\BillPayment($jobMailData)
                );
            }
        }

        $create_data = [
            'reservation_id' => $reservation_id,
            'total_amount' => $total_amount,
            'guests' => json_encode($set_guest),
        ];

        DBHelpers::create_query(ReservationSplitBills::class, $create_data);
    }
}
