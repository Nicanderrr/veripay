<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storage_files_can_be_served_through_fallback_route(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/test-image.jpg', 'fake-image-content');

        $this->get('/storage/products/test-image.jpg')
            ->assertOk()
            ->assertHeader('cache-control');
    }
}
