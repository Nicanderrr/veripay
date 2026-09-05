<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_adds_duplicate_scan_as_quantity_increment(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 5.00,
            'barcode' => 'TEST-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $service = new CartService();
        $cart = $service->addProduct($user, $product);
        $cart = $service->addProduct($user, $product);

        $this->assertCount(1, $cart->items);
        $this->assertEquals(2, $cart->items->first()->quantity);
    }
}
