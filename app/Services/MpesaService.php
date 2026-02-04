<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function stkPush(string $phoneNumber, float $amount, string $reference): array
    {
        $timestamp = now()->format('YmdHis');
        $shortCode = config('services.mpesa.shortcode');
        $passKey = config('services.mpesa.passkey');
        $callbackUrl = config('services.mpesa.callback_url');

        $password = base64_encode($shortCode . $passKey . $timestamp);

        $payload = [
            'BusinessShortCode' => $shortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $shortCode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => $reference,
            'TransactionDesc' => 'Laundry payment',
        ];

        $response = Http::withToken($this->accessToken())
            ->post(config('services.mpesa.stk_push_url'), $payload);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => $response->json('errorMessage') ?? 'Mpesa request failed.',
                'data' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'message' => 'STK Push initiated.',
            'data' => $response->json(),
        ];
    }

    protected function accessToken(): string
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret')
        )->get(config('services.mpesa.token_url'));

        return $response->json('access_token', '');
    }
}
