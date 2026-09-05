<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_order_history(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'History Product',
            'price' => 12.50,
            'barcode' => 'HIS-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 12.50,
            'status' => 'placed',
            'payment_status' => 'paid',
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 12.50,
            'total' => 12.50,
        ]);

        $this->actingAs($user)
            ->get(route('history'))
            ->assertOk()
            ->assertSee('Your orders');
    }
}
