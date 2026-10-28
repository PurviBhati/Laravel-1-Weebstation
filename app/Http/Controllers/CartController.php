<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CartStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        [$lines, $subtotal, $hasProblem] = CartStore::lines();

        return view('cart.index', compact('lines', 'subtotal', 'hasProblem'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:product,id',
            'variant_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::with('variants')
            ->where('status', 'active')
            ->find($data['product_id']);

        if (! $product) {
            return back()->with('error', 'This product is not available.');
        }

        $variantId = ! empty($data['variant_id']) ? (int) $data['variant_id'] : null;

        if ($product->variants->isNotEmpty()) {
            // Product with sizes: a size is required, and it must belong to this product.
            if (! $variantId) {
                return back()->with('error', 'Please choose a size.');
            }

            $variant = $product->variants->firstWhere('id', $variantId);

            if (! $variant) {
                return back()->with('error', 'That size is not available for this product.');
            }

            $available = (int) $variant->pivot->stock;
        } else {
            $variantId = null;
            $available = (int) $product->stock;
        }

        if ($available <= 0) {
            return back()->with('error', 'Sorry, this item is sold out.');
        }

        $key = $product->id.'-'.($variantId ?? 0);
        $cart = CartStore::get();
        $current = (int) ($cart[$key]['quantity'] ?? 0);
        $wanted = $current + (int) $data['quantity'];

        if ($wanted > $available) {
            $canAdd = max($available - $current, 0);

            $message = $canAdd > 0
                ? "Only {$available} available. You can add {$canAdd} more."
                : "You already have all {$available} available in your cart.";

            return back()->with('error', $message);
        }

        $cart[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'quantity' => $wanted,
        ];

        CartStore::put($cart);

        return back()->with('success', 'Added to your cart.');
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = CartStore::get();

        if (! isset($cart[$key])) {
            return redirect()->route('cart.index');
        }

        $wanted = (int) $request->input('quantity');
        $available = $this->availableStock($cart[$key]['product_id'], $cart[$key]['variant_id']);

        if ($available < 1) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'This item is out of stock now. Please remove it.');
        }

        $quantity = min($wanted, $available);

        $cart[$key]['quantity'] = $quantity;
        CartStore::put($cart);

        if ($quantity < $wanted) {
            return redirect()
                ->route('cart.index')
                ->with('error', "Only {$available} available, so the quantity was set to {$available}.");
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(string $key)
    {
        $cart = CartStore::get();

        unset($cart[$key]);

        CartStore::put($cart);

        return redirect()->route('user.cart.index')->with('success', 'Item removed.');
    }

    private function availableStock(int $productId, ?int $variantId): int
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
