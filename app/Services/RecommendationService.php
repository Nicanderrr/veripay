<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function recommendForProduct(Product $product, int $limit = 4): Collection
    {
        return Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
    }
}
