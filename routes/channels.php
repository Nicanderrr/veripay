<?php

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('cart.{cartId}', function (User $user, int $cartId) {
    return Cart::where('id', $cartId)->where('user_id', $user->id)->exists();
});

Broadcast::channel('admin.orders', function (User $user) {
    return $user->role === User::ROLE_ADMIN;
});
