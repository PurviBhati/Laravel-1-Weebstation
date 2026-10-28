<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartStore
{
    public static function get(): array
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return session('cart', []);
        }

        $cart = [];

        foreach (CartItem::where('customer_id', $customer->id)->orderBy('id')->get() as $item) {
            $cart[$item->product_id.'-'.($item->variant_id ?? 0)] = [
                'product_id' => (int) $item->product_id,
                'variant_id' => $item->variant_id ? (int) $item->variant_id : null,
                'quantity' => (int) $item->quantity,
            ];
        }

        return $cart;
    }

    public static function put(array $cart): void
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            session(['cart' => $cart]);

            return;
        }

        DB::transaction(function () use ($customer, $cart) {
            CartItem::where('customer_id', $customer->id)->delete();

            foreach ($cart as $item) {
                CartItem::create([
                    'customer_id' => $customer->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });
    }

    /**
     * The cart as lines for the cart page and the checkout.
     * Names, prices and stock come from the database, so they are always current.
     *
     * @return array{0: array, 1: float, 2: bool} [lines, subtotal, hasProblem]
     */
    public static function lines(): array
    {
        $cart = self::get();

        $products = Product::with(['images', 'variants'])
            ->whereIn('id', collect($cart)->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        $lines = [];
        $subtotal = 0.0;
        $hasProblem = false;

        foreach ($cart as $key => $item) {
            $product = $products->get($item['product_id']);

            // Deleted or no longer on sale.
            if (! $product || $product->status !== 'active') {
                $lines[] = ['key' => $key, 'missing' => true];
                $hasProblem = true;

                continue;
            }

            $variant = null;
            $available = (int) $product->stock;

            if ($item['variant_id']) {
                $variant = $product->variants->firstWhere('id', $item['variant_id']);

                // The size was removed from the product.
                if (! $variant) {
                    $lines[] = ['key' => $key, 'missing' => true];
                    $hasProblem = true;

                    continue;
                }

                $available = (int) $variant->pivot->stock;
            }

            $quantity = (int) $item['quantity'];
            $price = (float) $product->price;
            $lineTotal = round($price * $quantity, 2);
            $problem = null;

            if ($available <= 0) {
                $problem = 'Sold out';
            } elseif ($quantity > $available) {
                $problem = "Only {$available} left. Please lower the quantity.";
            }

            if ($problem) {
                $hasProblem = true;
            }

            $subtotal += $lineTotal;

            $lines[] = [
                'key' => $key,
                'missing' => false,
                'product' => $product,
                'size' => $variant?->value,
                'image' => $product->images->first()?->path,
                'price' => $price,
                'quantity' => $quantity,
                'available' => $available,
                'total' => $lineTotal,
                'problem' => $problem,
            ];
        }

        return [$lines, round($subtotal, 2), $hasProblem];
    }

    /**
     * Number of pieces in the cart (shown in the header).
     */
    public static function count(): int
    {
        return (int) collect(self::get())->sum('quantity');
    }

    public static function mergeGuestCart(): void
    {
        $guest = session('cart', []);

        if (! auth('customer')->check() || empty($guest)) {
            return;
        }

        $cart = self::get();

        foreach ($guest as $key => $item) {
            $available = self::availableStock($item['product_id'], $item['variant_id']);

            if ($available <= 0) {
                continue;
            }

            $current = (int) ($cart[$key]['quantity'] ?? 0);

            $cart[$key] = [
                'product_id' => (int) $item['product_id'],
                'variant_id' => $item['variant_id'] ? (int) $item['variant_id'] : null,
                'quantity' => min($current + (int) $item['quantity'], $available),
            ];
        }

        self::put($cart);
        session()->forget('cart');
    }

    private static function availableStock(int $productId, ?int $variantId): int
    {
        if ($variantId) {
            return (int) DB::table('product_variants')
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->value('stock');
        }

        return (int) Product::where('id', $productId)
            ->where('status', 'active')
            ->value('stock');
    }
}
