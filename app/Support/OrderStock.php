<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderStock
{
    public static function restore(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            if ($item->variant_id) {
                DB::table('product_variants')
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            }

            Product::where('id', $item->product_id)
                ->increment('stock', $item->quantity);
        }
    }
}
