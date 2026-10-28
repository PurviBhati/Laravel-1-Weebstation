<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use App\Support\CartStore;
use App\Support\Ordertotal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        [$lines, $subtotal, $hasProblem] = CartStore::lines();
        if (count($lines) === 0) {
            return redirect()->route('user.cart.index');
        }

        if ($hasProblem) {
            return redirect()->route('user.cart.index')->with('error', 'Please fix the items marked in red before you check out.');
        }

        return view('checkout.index', [
            'customer' => auth('customer')->user(),
            'lines' => $lines,
            'totals' => Ordertotal::for($subtotal),
        ]);
    }

    public function place(Request $request)
    {
        $customer = auth('customer')->user();

        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'customer_email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'payment_method' => 'required|in:cod,online_demo',
            'notes' => 'nullable|string|max:500',
        ], [
            'customer_phone.regex' => 'Enter a valid 10-digit mobile number',
        ]);
        $cart = CartStore::get();
        if (empty($cart)) {
            return redirect()->route('user.cart.index');
        }

        $order = DB::transaction(function () use ($cart, $data, $customer) {
            $lines = [];
            $subtotal = 0.0;

            foreach ($cart as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();
                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'An Item in your cart is no longer available. Please review your cart.',
                    ]);
                }

                $variant = null;
                if ($item['variant_id']) {
                    $variant = Variant::find($item['variant_id']);
                    $stockRow = DB::table('product_variants')
                        ->where('product_id', $product->id)
                        ->where('variant_id', $item['variant_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $variant || ! $stockRow) {
                        throw ValidationException::withMessages(['cart' => "A size in your cart is no longer available for {$product->name}."]);
                    }

                    $available = (int) $stockRow->stock;
                    $label = "{$product->name} ({$variant->value})";

                } else {
                    $hasSizes = DB::table('product_variants')
                        ->where('product_id', $product->id)
                        ->exists();

                    if ($hasSizes) {
                        throw ValidationException::withMessages(['cart' => "Please choose a size for {$product->name} in your cart."]);
                    }

                    $available = (int) $product->stock;
                    $label = $product->name;
                }

                if ($available < $item['quantity']) {
                    throw ValidationException::withMessages(['cart' => "Not enough stock for {$label}. Only {$available} left."]);
                }

                $price = (float) $product->price;
                $lineTotal = round($price * $item['quantity'], 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'size' => $variant?->value,
                    'price' => $price,
                    'quantity' => (int) $item['quantity'],
                    'line_total' => $lineTotal,
                ];
            }
            $subtotal = round($subtotal, 2);
            $totals = Ordertotal::for($subtotal);

            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'pincode' => $data['pincode'],
                'subtotal' => $subtotal,
                'shipping_charge' => $totals['shipping'],
                'gst_rate' => $totals['gst_rate'],
                'gst_amount' => $totals['gst'],
                'total' => $totals['total'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line);

                if ($line['variant_id']) {
                    DB::table('product_variants')
                        ->where('product_id', $line['product_id'])
                        ->where('variant_id', $line['variant_id'])
                        ->decrement('stock', $line['quantity']);
                }

                Product::where('id', $line['product_id'])
                    ->decrement('stock', $line['quantity']);
            }

            return $order;
        });

        CartStore::put([]);

        $this->rememberDetails($customer, $data);
        if ($order->payment_method === 'online_demo') {
            return redirect()
                ->route('orders.pay', $order)
                ->with('success', 'Order placed. Now complete the payment');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Thank you! Your order has beem placed.');

    }

    private function rememberDetails(Customer $customer, array $data): void
    {
        $updates = [
            'name' => $data['customer_name'],
            'address' => $data['address'],
            'city' => $data['city'],
            'pincode' => $data['pincode'],
        ];
        if (empty($customer->phone) && ! Customer::where('phone', $data['customer_phone'])->exists()) {
            $updates['phone'] = $data['customer_phone'];
        }

        $email = strtolower((string) ($data['customer_email'] ?? ''));

        if (empty($customer->email) && $email !== '' && ! Customer::where('email', $email)->exists()) {
            $updates['email'] = $email;
        }

        $customer->update($updates);
    }

    private function nextOrderNumber(): string
    {

        $prefix = 'ORD-'.now()->year.'-';
        $last = Order::where('order_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('order_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
