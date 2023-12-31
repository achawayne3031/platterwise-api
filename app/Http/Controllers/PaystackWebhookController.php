<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Paystack;
use App\Models\Transactions;
use App\Helpers\DBHelpers;
use App\Models\ReservationBills;
use App\Models\ReservationSplitBills;
use App\Models\Reservation;
use App\Models\Resturant;

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

        \Log::info('Init Transaction charge was success');

        if ($event->event == 'charge.success') {
            \Log::info('enter Transaction charge was success');

            // $paystack = Paystack::verifyTransaction($event->data->reference);

            //  if ($event->data->status) {

            if ($event->data->status == 'success') {
                \Log::info('Transaction status was success');
                $payment_data = json_encode($event->data);

                global $email;
                global $amount;
                global $guest_name;

                $amount = $event->data->amount;
                $email = $event->data->customer->email;

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
                \Log::info('Start Transaction data');

                $current_transaction = DBHelpers::query_filter_first(
                    Transactions::class,
                    [
                        'ref' => $ref,
                    ]
                );

                \Log::info('End Transaction data');

                $reservation_id = $current_transaction->reservation_id;
                $restaurant_id = $current_transaction->restaurant_id;

                $current_restaurant = DBHelpers::query_filter_first(
                    Resturant::class,
                    [
                        'id' => $restaurant_id,
                    ]
                );

                /////// Add Amount paid on Reservation Bill Table /////
                \Log::info('Start Get Reservation Bills data');

                $current_reservation_bill = DBHelpers::query_filter_first(
                    ReservationBills::class,
                    [
                        'reservation_id' => $reservation_id,
                    ]
                );

                \Log::info('End Get Reservation Bills data');

                $total_bill = $current_reservation_bill->total_bill;
                $amount_paid = $current_reservation_bill->amount_paid;
                $new_amount_paid = $amount_paid + $amount / 100;

                \Log::info('Start Update Reservation Bills amount_paid');

                DBHelpers::update_query_v3(
                    ReservationBills::class,
                    ['amount_paid' => $new_amount_paid],
                    [
                        'reservation_id' => $reservation_id,
                    ]
                );

                \Log::info('End Update Reservation Bills amount_paid');

                if ($new_amount_paid >= intval($total_bill)) {
                    DBHelpers::update_query_v3(
                        ReservationBills::class,
                        ['status' => 2],
                        [
                            'reservation_id' => $reservation_id,
                        ]
                    );

                    DBHelpers::update_query_v3(
                        Reservation::class,
                        ['status' => 4],
                        [
                            'id' => $reservation_id,
                        ]
                    );
                }

                ////// Update reservation split bill table with amount paid //////
                \Log::info('Start Get Reservation Spilt Bills ');

                $current_reservation_split_bill = DBHelpers::query_filter_first(
                    ReservationSplitBills::class,
                    [
                        'reservation_id' => $reservation_id,
                    ]
                );

                \Log::info('End Get Reservation Spilt Bills');

                $guests = json_decode($current_reservation_split_bill->guests);
                $new_guest_data = [];

                foreach ($guests as $value) {
                    if ($value->guest_email == $email) {
                        $guest_name = $value->guest_name;
                        $in_guest = [
                            'guest_email' => $value->guest_email,
                            'guest_name' => $value->guest_name,
                            'type' => $value->type,
                            'bill' => $value->bill,
                            'payment_url' => $value->payment_url,
                            'amount_paid' => $amount / 100,
                        ];
                        array_push($new_guest_data, $in_guest);
                    } else {
                        $in_guest = [
                            'guest_email' => $value->guest_email,
                            'guest_name' => $value->guest_name,
                            'type' => $value->type,
                            'bill' => $value->bill,
                            'payment_url' => $value->payment_url,
                            'amount_paid' => $value->amount_paid,
                        ];

                        array_push($new_guest_data, $in_guest);
                    }
                }

                $cre = Carbon::now();
                $formattedTime = $cre->toDayDateTimeString();
                $array_time = explode(' ', $formattedTime);
                $payment_date = $cre->toFormattedDateString();

                $paymentCompletedData = [
                    'restaurant_name' => $current_restaurant->name,
                    'guest_name' => $guest_name,
                    'amount' => $amount / 100,
                    'payment_date' => $payment_date,
                ];

                \Mail::to($email)->send(
                    new \App\Mail\PaymentCompleted($paymentCompletedData)
                );

                \Log::info('Start Update Reservation Spilt Bills');
                \Log::info($new_guest_data);

                $encoded_guest = json_encode($new_guest_data);

                DBHelpers::update_query_v3(
                    ReservationSplitBills::class,
                    ['guests' => $encoded_guest],
                    [
                        'reservation_id' => $reservation_id,
                    ]
                );

                \Log::info('End Update Reservation Spilt Bills');
            } else {
                DBHelpers::update_query_v3(
                    Transactions::class,
                    ['status' => 0],
                    [
                        'ref' => $ref,
                    ]
                );

                \Log::info('Transaction reference not found');
            }
            //  }
        } else {
            DBHelpers::update_query_v3(
                Transactions::class,
                ['status' => 0],
                [
                    'ref' => $ref,
                ]
            );
        }

        exit();
    }
}
