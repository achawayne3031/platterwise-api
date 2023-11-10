<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $event = json_decode($input);

        exit();
    }
}
