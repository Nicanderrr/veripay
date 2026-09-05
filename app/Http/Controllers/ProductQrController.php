<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductQrCode;

class ProductQrController extends Controller
{
    public function show(Product $product, ProductQrCode $productQrCode)
    {
        abort_unless($product->is_active, 404);

        return response($productQrCode->svg($product), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
