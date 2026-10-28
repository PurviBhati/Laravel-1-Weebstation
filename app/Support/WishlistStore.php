<?php

namespace App\Support;

use App\Models\WishlistItem;
use Illuminate\Support\Facades\DB;

class WishlistStore
{
    public static function get(): array
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return array_map('intval', session('wishlist', []));
        }

        return WishlistItem::where('customer_id', $customer->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function put(array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $customer = auth('customer')->user();

        if (! $customer) {
            session(['wishlist' => $ids]);

            return;
        }

        DB::transaction(function () use ($customer, $ids) {
            WishlistItem::where('customer_id', $customer->id)->delete();

            foreach ($ids as $id) {
                WishlistItem::create([
                    'customer_id' => $customer->id,
                    'product_id' => $id,
                ]);
            }
        });
    }

    public static function count(): int
    {
        return count(self::get());
    }

    public static function mergeGuestWishlist(): void
    {
        $guest = session('wishlist', []);

        if (! auth('customer')->check() || empty($guest)) {
            return;
        }

        self::put(array_merge(self::get(), array_map('intval', $guest)));
        session()->forget('wishlist');
    }
}
