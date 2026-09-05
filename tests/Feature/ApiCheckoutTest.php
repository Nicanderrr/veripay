<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Mail\OrderReceiptMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_checkout(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'name' => 'Checkout API',
            'price' => 5.00,
            'barcode' => 'API-CHK-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/api/checkout', [
            'provider' => 'card',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Checkout successful']);

        $order = Order::first();
        $this->assertNotNull($order->receipt_token);
        $this->assertNotNull($order->receipt_sent_at);
        Mail::assertSent(OrderReceiptMail::class);
    }

    public function test_security_can_verify_receipt(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $security = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Sanctum::actingAs($user);

        $product = Product::create([
            'name' => 'Security Receipt Product',
            'price' => 9.00,
            'barcode' => 'SEC-CHK-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson('/api/checkout', [
            'provider' => 'card',
        ])->assertOk();

        $order = Order::firstOrFail();

        $this->actingAs($security)
            ->post(route('security.receipts.verify', $order->receipt_token))
            ->assertRedirect(route('security.receipts.show', $order->receipt_token));

        $this->assertNotNull($order->fresh()->receipt_verified_at);
    }
}
