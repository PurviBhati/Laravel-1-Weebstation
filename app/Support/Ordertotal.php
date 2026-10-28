<?php

namespace App\Support;

class Ordertotal
{
    public static function for(float $subtotal): array
    {
        $subtotal = round($subtotal, 2);
        $freeAbove = (float) config('shop.free_shipping_above', 500);
        $charge = (float) config('shop.shipping_charge', 250);
        $rate = (float) config('shop.gst_rate', 5);

        $isFree = $subtotal > $freeAbove;
        $shipping = $isFree ? 0.0 : $charge;
        $gst = round($subtotal * $rate / 100, 2);

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'gst_rate' => $rate,
            'gst' => $gst,
            'total' => round($subtotal + $shipping + $gst, 2),
            'to_free' => $isFree ? 0 : (int) floor($freeAbove - $subtotal) + 1,
        ];
    }
}
