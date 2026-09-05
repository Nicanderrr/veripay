<?php

namespace App\Services;

use App\Events\CartUpdated;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getActiveCart(User $user): Cart
    {
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
                'currency' => 'GHS',
            ]);
        }

        return $cart->fresh(['items.product']);
    }

    public function addByBarcode(User $user, string $barcode, int $quantity = 1): Cart
    {
        $product = Product::where('barcode', $barcode)->where('is_active', true)->firstOrFail();

        return $this->addProduct($user, $product, $quantity);
    }

    public function addProduct(User $user, Product $product, int $quantity = 1): Cart
    {
        return DB::transaction(function () use ($user, $product, $quantity) {
            $cart = $this->getActiveCart($user);

            $item = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if ($item) {
                $item->quantity += $quantity;
            } else {
                $item = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total' => 0,
                ]);
            }

            $item->unit_price = $product->price;
            $item->total = $item->quantity * $item->unit_price;
            $item->save();

            $this->recalculate($cart);

            event(new CartUpdated($cart->fresh('items.product')));

            return $cart->fresh('items.product');
        });
    }

    public function removeProduct(User $user, Product $product, int $quantity = 1): Cart
    {
        return DB::transaction(function () use ($user, $product, $quantity) {
            $cart = $this->getActiveCart($user);

            $item = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if ($item) {
                $item->quantity -= $quantity;
                if ($item->quantity <= 0) {
                    $item->delete();
                } else {
                    $item->total = $item->quantity * $item->unit_price;
                    $item->save();
                }
            }

            $this->recalculate($cart);

            event(new CartUpdated($cart->fresh('items.product')));

            return $cart->fresh('items.product');
        });
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update([
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ]);

        event(new CartUpdated($cart->fresh('items.product')));
    }

    public function recalculate(Cart $cart): void
    {
        $subtotal = $cart->items()->sum('total');
        $tax = 0;
        $total = $subtotal + $tax;

        $cart->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function toPayload(Cart $cart, ?User $user = null): array
    {
        $cart->loadMissing('items.product.category');

        $budgetLimit = $user?->budget_limit;
        $budgetWarning = false;
        if ($budgetLimit !== null && $cart->total > $budgetLimit) {
            $budgetWarning = true;
        }

        return [
            'id' => $cart->id,
            'status' => $cart->status,
            'subtotal' => $cart->subtotal,
            'tax' => $cart->tax,
            'total' => $cart->total,
            'currency' => $cart->currency,
            'budget_limit' => $budgetLimit,
            'budget_warning' => $budgetWarning,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product?->name,
                    'barcode' => $item->product?->barcode,
                    'image_path' => $item->product?->image_path,
                    'category' => $item->product?->category ? [
                        'id' => $item->product->category->id,
                        'name' => $item->product->category->name,
                    ] : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ];
            })->values(),
        ];
    }
}
