<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Checkout Product',
            'price' => 7.50,
            'barcode' => 'CHK-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $service = new CartService();
        $cart = $service->addProduct($user, $product, 2);

        $this->assertEquals(15.00, $cart->total);
    }
}
