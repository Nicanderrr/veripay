<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_product_qr_code_is_generated_from_barcode(): void
    {
        $product = Product::create([
            'name' => 'Scan Test Product',
            'price' => 12.50,
            'barcode' => 'TEST-QR-1001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->get(route('products.qr', $product));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertNotEmpty($response->getContent());
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_inactive_product_qr_code_is_not_public(): void
    {
        $product = Product::create([
            'name' => 'Inactive Product',
            'price' => 12.50,
            'barcode' => 'TEST-QR-1002',
            'stock_quantity' => 10,
            'is_active' => false,
        ]);

        $this->get(route('products.qr', $product))->assertNotFound();
    }

    public function test_admin_product_list_embeds_qr_code_without_remote_image_request(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Product::create([
            'name' => 'Embedded QR Product',
            'price' => 20,
            'barcode' => 'EMBED-QR-1001',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('data:image/svg+xml;base64,', false)
            ->assertSee('EMBED-QR-1001');
    }
}
