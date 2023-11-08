<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Paystack
{
    private static function httpRequest()
    {
        $request = Http::baseUrl('https://api.paystack.co/');
        $signature = 'Bearer ' . env('PAYSTACK_SECRET_KEY_TEST');
        return $request->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $signature,
        ]);
    }

    public static function verifyTransaction($reference)
    {
        $verifyTransaction = self::httpRequest()->get(
            'transaction/verify/' . $reference
        );
        return json_decode($verifyTransaction->getBody());
    }

    public static function transferRecipient($data)
    {
        $transferRecipient = self::httpRequest()->post(
            'transferrecipient',
            $data
        );
        return json_decode($transferRecipient->getBody());
    }

    public static function intializeTransaction($data)
    {
        $intializeTransaction = self::httpRequest()->post(
            'transaction/initialize',
            $data
        );
        return json_decode($intializeTransaction->getBody());
    }
}
