<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\CartStore;
use App\Support\OrderStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::withCount('items')
            ->where('customer_id', auth('customer')->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->ownOrder($order);
        $order->load('items');

        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order): View
    {
        $this->ownOrder($order);
        $order->load('items');

        return view('orders.invoice', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->ownOrder($order);

        $cancelled = DB::transaction(function () use ($order): bool {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->ownOrder($lockedOrder);

            if (! in_array($lockedOrder->status, ['pending', 'confirmed'], true)) {
                return false;
            }

            OrderStock::restore($lockedOrder);
            $lockedOrder->update([
                'status' => 'cancelled',
                'payment_status' => $lockedOrder->payment_status === 'paid'
                    ? 'refunded'
                    : $lockedOrder->payment_status,
            ]);

            return true;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with(
                $cancelled ? 'success' : 'error',
                $cancelled
                    ? 'Your order has been cancelled.'
                    : 'This order can no longer be cancelled.',
            );
    }

    public function reorder(Order $order): RedirectResponse
    {
        $this->ownOrder($order);
        $order->load('items');

        $cart = CartStore::get();
        $skippedItems = [];
        $addedQuantity = 0;

        foreach ($order->items as $item) {
            if (! $item->product_id) {
                $skippedItems[] = $item->product_name;

                continue;
            }

            $product = Product::query()
                ->whereKey($item->product_id)
                ->where('status', 'active')
                ->first();

            if (! $product) {
                $skippedItems[] = $item->product_name;

                continue;
            }

            $variantId = $item->variant_id ? (int) $item->variant_id : null;
            $key = $product->id.'-'.($variantId ?? 0);
            $currentQuantity = (int) ($cart[$key]['quantity'] ?? 0);

            if ($variantId) {
                $variantStock = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variantId)
                    ->value('stock');

                if ($variantStock === null) {
                    $skippedItems[] = $item->product_name;

                    continue;
                }

                $availableStock = (int) $variantStock;
            } else {
                $availableStock = (int) $product->stock;
            }

            $quantityToAdd = min(
                (int) $item->quantity,
                max($availableStock - $currentQuantity, 0),
            );

            if ($quantityToAdd === 0) {
                $skippedItems[] = $item->product_name;

                continue;
            }

            $cart[$key] = [
                'product_id' => (int) $product->id,
                'variant_id' => $variantId,
                'quantity' => $currentQuantity + $quantityToAdd,
            ];
            $addedQuantity += $quantityToAdd;
        }

        if ($addedQuantity === 0) {
            return redirect()
                ->route('user.cart.index')
                ->with('error', 'None of the items in this order are currently available.');
        }

        CartStore::put($cart);

        return redirect()
            ->route('user.cart.index')
            ->with(
                count($skippedItems) > 0 ? 'error' : 'success',
                count($skippedItems) > 0
                    ? 'Available items were added to your cart. Some items could not be added because they are unavailable or out of stock.'
                    : 'The items from your order were added to your cart.',
            );
    }

    public function pay(Order $order)
    {
        $this->ownOrder($order);

        if (! $this->canPay($order)) {
            return redirect()->route('orders.show', $order);
        }

        return view('orders.pay', compact('order'));
    }

    public function processPay(Request $request, Order $order)
    {
        $this->ownOrder($order);

        $request->validate(['result' => 'required|in:success,fail,cod']);
        if (! $this->canPay($order)) {
            return redirect()->route('orders.show', $order);
        }

        $result = $request->input('result');
        if ($result === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'confirmed',
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Payment successful. Your order is confirmed.');
        }

        if ($result === 'cod') {
            $order->update([
                'payment_method' => 'cod',
                'payment_status' => 'pending',
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Pay when the order is delivered.');
        }

        $order->update(['payment_status' => 'failed']);

        return redirect()
            ->route('orders.pay', $order)
            ->with('error', 'Payment failed. You can try again or pay on delivery.');
    }

    private function ownOrder(Order $order): void
    {
        abort_unless($order->customer_id === auth('customer')->id(), 403);
    }

    private function canPay(Order $order): bool
    {
        return $order->payment_method === 'online_demo'
            && $order->payment_status !== 'paid'
            && $order->status !== 'cancelled';
    }
}
