<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'Personal Care'])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Personal Care')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), ['name' => 'Health Care'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Health Care']);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category->fresh()))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['name' => 'Health Care']);
    }

    public function test_admin_can_view_customer_order_receipt_details(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create([
            'name' => 'Receipt Customer',
            'email' => 'receipt@example.test',
        ]);
        $product = Product::create([
            'name' => 'Receipt Product',
            'price' => 15,
            'barcode' => 'RECEIPT-QR-001',
            'stock_quantity' => 3,
            'is_active' => true,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 30,
            'status' => 'fulfilled',
            'payment_status' => 'paid',
            'receipt_token' => 'ADMIN-RECEIPT-TOKEN',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 15,
            'total' => 30,
        ]);
        Payment::create([
            'order_id' => $order->id,
            'provider' => 'paystack',
            'amount' => 30,
            'status' => 'paid',
            'reference' => 'PAYSTACK-REF-001',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Receipt Customer')
            ->assertSee('receipt@example.test')
            ->assertSee('Receipt Product')
            ->assertSee('ADMIN-RECEIPT-TOKEN')
            ->assertSee('PAYSTACK-REF-001');
    }
}
