<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request, CartService $cartService)
    {
        $cart = $cartService->getActiveCart($request->user());

        return response()->json($cartService->toPayload($cart, $request->user()));
    }

    public function add(Request $request, CartService $cartService)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $product = Product::findOrFail($request->integer('product_id'));
        $cart = $cartService->addProduct($request->user(), $product, $request->integer('quantity', 1));

        return response()->json($cartService->toPayload($cart, $request->user()));
    }

    public function remove(Request $request, CartService $cartService)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $product = Product::findOrFail($request->integer('product_id'));
        $cart = $cartService->removeProduct($request->user(), $product, $request->integer('quantity', 1));

        return response()->json($cartService->toPayload($cart, $request->user()));
    }

    public function scan(Request $request, CartService $cartService)
    {
        $request->validate([
            'qr_code' => 'required_without:barcode|string',
            'barcode' => 'required_without:qr_code|string',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $qrCode = $request->string('qr_code')->toString() ?: $request->string('barcode')->toString();
        $cart = $cartService->addByBarcode($request->user(), $qrCode, $request->integer('quantity', 1));

        return response()->json($cartService->toPayload($cart, $request->user()));
    }
}
