<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\OrderFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Throwable;

class PaystackPaymentController extends Controller
{
    public function callback(Request $request, PaystackService $paystack, OrderFulfillmentService $fulfillment)
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()->route('checkout')->with('error', 'Missing Paystack payment reference.');
        }

        $payment = Payment::with('order.items.product', 'order.user', 'order.cart')
            ->where('reference', $reference)
            ->first();

        if (! $payment || ! $payment->order) {
            return redirect()->route('checkout')->with('error', 'Payment reference was not found.');
        }

        try {
            $verification = $paystack->verify($reference);
        } catch (Throwable $exception) {
            $payment->update([
                'status' => 'failed',
                'payload' => ['error' => $exception->getMessage()],
            ]);

            return redirect()->route('checkout')->with('error', $exception->getMessage());
        }

        if (! $paystack->isSuccessfulForOrder($verification, $payment->order)) {
            $payment->update([
                'status' => 'failed',
                'payload' => $verification,
            ]);
            $payment->order->update(['status' => 'payment_failed', 'payment_status' => 'failed']);

            return redirect()->route('checkout')->with('error', 'Paystack payment could not be verified.');
        }

        $payment->update([
            'status' => 'paid',
            'payload' => $verification,
            'paid_at' => now(),
        ]);

        $fulfillment->fulfillPaidOrder($payment->order);

        return redirect()->route('home')->with('success', 'Payment verified. Your receipt has been emailed.');
    }
}
