<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiateMpesa(Request $request, LaundryOrder $order, MpesaService $mpesaService)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $response = $mpesaService->stkPush(
            $validated['phone_number'],
            $validated['amount'],
            $order->order_number
        );

        if (! $response['success']) {
            return back()->withErrors(['mpesa' => $response['message']]);
        }

        Payment::create([
            'laundry_order_id' => $order->id,
            'method' => 'mpesa',
            'amount' => $validated['amount'],
            'merchant_request_id' => $response['data']['MerchantRequestID'] ?? null,
            'checkout_request_id' => $response['data']['CheckoutRequestID'] ?? null,
        ]);

        return back()->with('status', 'STK Push request sent.');
    }

    public function mpesaCallback(Request $request)
    {
        $payload = $request->input('Body.stkCallback');

        if (! $payload) {
            return response()->json(['message' => 'Invalid callback'], 400);
        }

        $checkoutRequestId = $payload['CheckoutRequestID'] ?? null;
        $payment = Payment::query()->where('checkout_request_id', $checkoutRequestId)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $resultCode = (string) ($payload['ResultCode'] ?? '1');
        $payment->update([
            'result_code' => $resultCode,
            'result_desc' => $payload['ResultDesc'] ?? null,
            'paid_at' => now(),
        ]);

        if ($resultCode === '0') {
            $metadata = collect($payload['CallbackMetadata']['Item'] ?? [])
                ->keyBy('Name')
                ->map(fn ($item) => $item['Value'] ?? null);

            $payment->update([
                'mpesa_receipt' => $metadata->get('MpesaReceiptNumber'),
                'reference' => $metadata->get('PhoneNumber'),
            ]);

            $order = $payment->order;
            $order->update([
                'paid_amount' => $order->paid_amount + $payment->amount,
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
