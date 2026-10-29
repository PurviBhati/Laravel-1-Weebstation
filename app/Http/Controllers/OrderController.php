<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\CartStore;
use App\Support\OrderStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function invoice(Order $order)
    {
        $this->ownOrder($order);

        $order->load('items');

        return view('orders.invoice', compact('order'));
    }

    public function cancel(Order $order)
    {
        $this->ownOrder($order);

        $wasPaid = false;

        $cancelled = DB::transaction(function () use ($order, &$wasPaid) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! in_array($locked->status, ['pending', 'confirmed'], true)) {
                return false;
            }

            OrderStock::restore($locked);

            $wasPaid = $locked->payment_status === 'paid';

            $locked->update([
                'status' => 'cancelled',
                'cancelled_by' => 'customer',
                'cancelled_at' => now(),
                'payment_status' => $wasPaid ? 'refunded' : $locked->payment_status,
            ]);

            return true;
        });

        if (! $cancelled) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'This order can no longer be cancelled because it is already on its way.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $wasPaid
                ? 'Your order was cancelled. The payment will be refunded (demo).'
                : 'Your order was cancelled.');
    }

    public function editAddress(Order $order)
    {
        $this->ownOrder($order);

        if (! $this->canChangeAddress($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'The address can no longer be changed because the order is already on its way.');
        }

        return view('orders.address', compact('order'));
    }

    public function updateAddress(Request $request, Order $order)
    {
        $this->ownOrder($order);

        if (! $this->canChangeAddress($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'The address can no longer be changed because the order is already on its way.');
        }

        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
        ], [
            'customer_phone.regex' => 'Enter a valid 10-digit mobile number.',
        ]);

        $changed = false;

        foreach ($data as $field => $value) {
            if ((string) $order->{$field} !== (string) $value) {
                $changed = true;
                break;
            }
        }

        if (! $changed) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Nothing was changed.');
        }

        $order->update($data + [
            'address_updated_at' => now(),
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Delivery address updated. The shop has been told.');
    }

    public function reorder(Order $order)
    {
        $this->ownOrder($order);

        $order->load('items');

        $cart = CartStore::get();
        $added = 0;
        $skipped = [];

        foreach ($order->items as $item) {
            $product = $item->product_id
                ? Product::with('variants')->where('status', 'active')->find($item->product_id)
                : null;

            if (! $product) {
                $skipped[] = $item->product_name;

                continue;
            }

            $variantId = null;

            if ($product->variants->isNotEmpty()) {
                $variant = $item->variant_id ? $product->variants->firstWhere('id', $item->variant_id) : null;

                if (! $variant) {
                    $skipped[] = $item->product_name;

                    continue;
                }

                $variantId = $variant->id;
                $available = (int) $variant->pivot->stock;
            } else {
                $available = (int) $product->stock;
            }

            $key = $product->id.'-'.($variantId ?? 0);
            $current = (int) ($cart[$key]['quantity'] ?? 0);
            $room = $available - $current;

            if ($room <= 0) {
                $skipped[] = $item->product_name;

                continue;
            }

            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'quantity' => $current + min((int) $item->quantity, $room),
            ];

            $added++;
        }

        CartStore::put($cart);

        if ($added === 0) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Sorry, none of these items are available right now.');
        }

        $message = $added.($added === 1 ? ' item was' : ' items were').' added to your cart.';

        if ($skipped) {
            $message .= ' Not available now: '.implode(', ', $skipped).'.';
        }

        return redirect()->route('user.cart.index')->with('success', $message);
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

        $request->validate([
            'result' => 'required|in:success,fail,cod',
        ]);

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
                ->with('success', 'Okay, you will pay when the order is delivered.');
        }

        $order->update(['payment_status' => 'failed']);

        return redirect()
            ->route('orders.pay', $order)
            ->with('error', 'The payment failed. You can try again or pay on delivery.');
    }

    private function ownOrder(Order $order): void
    {
        abort_unless((int) $order->customer_id === (int) auth('customer')->id(), 404);
    }

    private function canChangeAddress(Order $order): bool
    {
        return in_array($order->status, ['pending', 'confirmed'], true);
    }

    private function canPay(Order $order): bool
    {
        return $order->payment_method === 'online_demo'
            && $order->payment_status !== 'paid'
            && $order->status !== 'cancelled';
    }
}
