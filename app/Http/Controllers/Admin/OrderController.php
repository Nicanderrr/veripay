<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment'])->orderByDesc('created_at');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.category', 'payment', 'receiptVerifier']);

        return view('admin.orders.show', compact('order'));
    }
}
