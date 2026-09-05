<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Jobs\ProcessOrderJob;
use App\Mail\OrderReceiptMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderFulfillmentService
{
    public function fulfillPaidOrder(Order $order): Order
    {
        $order->loadMissing(['cart', 'items.product', 'payment', 'user']);

        $shouldFulfill = ! $order->fulfilled_at;
        $shouldSendReceipt = ! $order->receipt_sent_at && (bool) $order->user?->email;

        if (! $order->receipt_token) {
            $order->receipt_token = $this->newReceiptToken();
        }

        $order->forceFill([
            'status' => 'placed',
            'payment_status' => 'paid',
            'placed_at' => $order->placed_at ?: now(),
            'fulfilled_at' => $order->fulfilled_at ?: now(),
        ])->save();

        if ($shouldFulfill && $order->cart && $order->cart->status !== 'checked_out') {
            $order->cart->items()->delete();
            $order->cart->update([
                'status' => 'checked_out',
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);
        }

        if ($shouldFulfill) {
            ProcessOrderJob::dispatch($order);
            event(new OrderPlaced($order));
        }

        if ($shouldSendReceipt) {
            Mail::to($order->user)->send(new OrderReceiptMail($order->fresh(['items.product', 'payment', 'user'])));

            $order->forceFill([
                'receipt_sent_at' => now(),
            ])->save();
        }

        return $order->fresh(['items.product', 'payment', 'user']);
    }

    private function newReceiptToken(): string
    {
        do {
            $token = Str::upper(Str::random(48));
        } while (Order::where('receipt_token', $token)->exists());

        return $token;
    }
}
