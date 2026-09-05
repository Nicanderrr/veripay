<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        $perPage = min(max($request->integer('per_page', 20), 1), 100);

        return response()->json($query->with('category')->inRandomOrder()->paginate($perPage));
    }

    public function show(Product $product, RecommendationService $recommendations)
    {
        $product->load('category');

        return response()->json([
            'product' => $product,
            'recommendations' => $recommendations->recommendForProduct($product),
        ]);
    }
}
