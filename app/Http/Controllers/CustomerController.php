<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\RecommendationService;

class CustomerController extends Controller
{
    public function home()
    {
        $products = Product::with('category')->where('is_active', true)->latest()->take(8)->get();
        $trending = Product::with('category')->where('is_active', true)->inRandomOrder()->take(10)->get();
        $categories = Category::withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('customer.home', compact('products', 'trending', 'categories'));
    }

    public function scan()
    {
        return view('customer.scan');
    }

    public function cart()
    {
        return view('customer.cart');
    }

    public function history()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.history', compact('orders'));
    }

    public function checkout()
    {
        return view('customer.checkout');
    }

    public function shop()
    {
        $categories = Category::orderBy('name')->get();

        return view('customer.shop', compact('categories'));
    }

    public function product(Product $product, RecommendationService $recommendations)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        return view('customer.product', [
            'product' => $product,
            'recommendations' => $recommendations->recommendForProduct($product, 8),
        ]);
    }

}
