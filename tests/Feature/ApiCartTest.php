<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_to_cart_via_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'name' => 'API Product',
            'price' => 3.50,
            'barcode' => 'API-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['total' => '7.00']);
    }

    public function test_can_scan_product_with_qr_code_payload(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Product::create([
            'name' => 'QR Scan Product',
            'price' => 4.25,
            'barcode' => 'QR-PAYLOAD-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/scan', [
            'qr_code' => 'QR-PAYLOAD-001',
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'QR Scan Product',
            'barcode' => 'QR-PAYLOAD-001',
        ]);
    }
}
