<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Paystack;
use App\Models\Transactions;
use App\Helpers\DBHelpers;
use App\Models\ReservationBills;
use App\Models\ReservationSplitBills;

class PaystackWebhookController extends Controller
{
    //

    public function handleWebhook(Request $request)
    {
        // only a post with paystack signature header gets our attention
        if (
            strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ||
            !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)
        ) {
            exit();
        }

        // Retrieve the request's body
        $input = @file_get_contents('php://input');

        // validate event do all at once to avoid timing attack
        if (
            $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !==
            hash_hmac('sha512', $input, env('PAYSTACK_SECRET_KEY_TEST'))
        ) {
            exit();
        }

        $webhook_data = json_decode($input);
        $event = json_decode($input);
        $ref = $event->data->reference;

        DBHelpers::update_query_v3(
            Transactions::class,
            ['webhook_extra' => json_encode($webhook_data)],
            [
                'ref' => $ref,
            ]
        );

        if ($event->event == 'charge.success') {
            $paystack = Paystack::verifyTransaction($event->data->reference);
            if ($paystack->status) {
                if ($paystack->data->status == 'success') {
                    $payment_data = json_encode($paystack->data);

                    global $email;
                    global $amount;

                    $amount = $paystack->data->amount;
                    $email = $paystack->data->customer->email;

                    DBHelpers::update_query_v3(
                        Transactions::class,
                        [
                            'status' => 3,
                            'payment_extra' => $payment_data,
                            'amount_paid' => $amount,
                        ],
                        ['ref' => $ref]
                    );

                    ////// Get current Transaction from Transaction Table //////
                    $current_transaction = DBHelpers::query_filter_first(
                        Transactions::class,
                        [
                            'ref' => $ref,
                        ]
                    );

                    $reservation_id = $current_transaction->reservation_id;
                    $restaurant_id = $current_transaction->restaurant_id;

                    /////// Add Amount paid on Reservation Bill Table /////
                    $current_reservation_bill = DBHelpers::query_filter_first(
                        ReservationBills::class,
                        [
                            'reservation_id' => $reservation_id,
                        ]
                    );

                    $total_bill = $current_reservation_bill->total_bill;
                    $amount_paid = $current_reservation_bill->amount_paid;
                    $new_amount_paid =
                        floatval($amount) + floatval($amount_paid);
                    DBHelpers::update_query_v3(
                        ReservationBills::class,
                        ['amount_paid' => $new_amount_paid],
                        [
                            'reservation_id' => $reservation_id,
                        ]
                    );

                    if ($new_amount_paid >= floatval($total_bill)) {
                        DBHelpers::update_query_v3(
                            ReservationBills::class,
                            ['status' => 4],
                            [
                                'reservation_id' => $reservation_id,
                            ]
                        );
                    }

                    ////// Update reservation split bill table with amount paid //////
                    $current_reservation_split_bill = DBHelpers::query_filter_first(
                        ReservationSplitBills::class,
                        [
                            'reservation_id' => $reservation_id,
                        ]
                    );

                    $guests = json_decode(
                        $current_reservation_split_bill->guests
                    );
                    $new_guest_data = [];

                    foreach ($guests as $value) {
                        $in_guest = [
                            'guest_email' => $value['guest_email'],
                            'guest_name' => $value['guest_name'],
                            'type' => $value['type'],
                            'bill' => $value['bill'],
                            'payment_url' => $value['auth_url'],
                            'amount_paid' => $amount,
                        ];

                        array_push($new_guest_data, $in_guest);
                    }

                    DBHelpers::update_query_v3(
                        ReservationSplitBills::class,
                        ['guests' => json_encode($new_guest_data)],
                        [
                            'reservation_id' => $reservation_id,
                        ]
                    );
                } else {
                    $this->logger->error('Transaction reference not found');
                }
            }
        } else {
        }

        exit();
    }
}
