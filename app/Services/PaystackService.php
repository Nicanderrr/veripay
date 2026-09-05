<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PaystackService
{
    public function initialize(Order $order, string $callbackUrl): array
    {
        $this->ensureConfigured();

        $order->loadMissing('user');
        $reference = 'SN-' . $order->id . '-' . Str::upper(Str::random(12));
        $amount = (int) round(((float) $order->total) * 100);

        $response = Http::withToken((string) config('services.paystack.secret_key'))
            ->acceptJson()
            ->post($this->url('/transaction/initialize'), [
                'email' => $order->user?->email,
                'amount' => $amount,
                'currency' => config('services.paystack.currency', 'GHS'),
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'order_id' => $order->id,
                    'cart_id' => $order->cart_id,
                ],
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException($response->json('message') ?: 'Paystack initialization failed.');
        }

        return [
            'reference' => $reference,
            'authorization_url' => $response->json('data.authorization_url'),
            'access_code' => $response->json('data.access_code'),
            'payload' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        $this->ensureConfigured();

        $response = Http::withToken((string) config('services.paystack.secret_key'))
            ->acceptJson()
            ->get($this->url('/transaction/verify/' . rawurlencode($reference)));

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException($response->json('message') ?: 'Paystack verification failed.');
        }

        return $response->json();
    }

    public function isSuccessfulForOrder(array $verification, Order $order): bool
    {
        $status = data_get($verification, 'data.status');
        $amount = (int) data_get($verification, 'data.amount', 0);
        $expectedAmount = (int) round(((float) $order->total) * 100);

        return $status === 'success' && $amount === $expectedAmount;
    }

    private function ensureConfigured(): void
    {
        if (! config('services.paystack.secret_key')) {
            throw new RuntimeException('Paystack is not configured. Add PAYSTACK_SECRET_KEY to the environment.');
        }
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.paystack.base_url', 'https://api.paystack.co'), '/') . $path;
    }
}
