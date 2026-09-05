<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\OrderFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckoutController extends Controller
{
    public function checkout(
        Request $request,
        CartService $cartService,
        OrderFulfillmentService $fulfillment,
        PaystackService $paystack
    ) {
        $request->validate([
            'provider' => 'required|string|in:card,paystack',
        ]);

        $user = $request->user();
        $cart = $cartService->getActiveCart($user);
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $provider = (string) $request->string('provider');

        $order = DB::transaction(function () use ($cart, $user, $provider) {
            $order = Order::create([
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'total' => $cart->total,
                'status' => $provider === 'paystack' ? 'pending_payment' : 'placed',
                'payment_status' => $provider === 'paystack' ? 'pending' : 'paid',
                'placed_at' => $provider === 'paystack' ? null : now(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'provider' => $provider,
                'amount' => $cart->total,
                'status' => $provider === 'paystack' ? 'pending' : 'paid',
                'reference' => $provider === 'paystack' ? null : 'MOCK-' . strtoupper(bin2hex(random_bytes(4))),
                'payload' => ['method' => $provider === 'paystack' ? 'paystack' : 'mock'],
                'paid_at' => $provider === 'paystack' ? null : now(),
            ]);

            return $order->fresh(['items.product', 'payment', 'user', 'cart']);
        });

        if ($provider === 'paystack') {
            try {
                $initialized = $paystack->initialize($order, route('payments.paystack.callback'));
            } catch (Throwable $exception) {
                $order->payment?->update([
                    'status' => 'failed',
                    'payload' => ['error' => $exception->getMessage()],
                ]);
                $order->update(['status' => 'payment_failed', 'payment_status' => 'failed']);

                return response()->json(['message' => $exception->getMessage()], 422);
            }

            $order->payment?->update([
                'reference' => $initialized['reference'],
                'payload' => $initialized['payload'],
            ]);

            return response()->json([
                'message' => 'Paystack payment initialized',
                'order_id' => $order->id,
                'authorization_url' => $initialized['authorization_url'],
                'reference' => $initialized['reference'],
            ]);
        }

        $fulfillment->fulfillPaidOrder($order);

        return response()->json([
            'message' => 'Checkout successful',
            'order_id' => $order->id,
        ]);
    }
}
