<?php

namespace App\Jobs;

use App\Models\InventoryLog;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(): void
    {
        $this->order->load('items.product');

        foreach ($this->order->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            $before = $product->stock_quantity;
            $after = max(0, $before - $item->quantity);

            $product->update(['stock_quantity' => $after]);

            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => $this->order->user_id,
                'change' => -1 * $item->quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'reason' => 'order_checkout',
            ]);
        }
    }
}
