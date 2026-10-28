@extends('layouts.storefront')

@section('title', $order->order_number . ' | WEEB STATION')

@section('account')

@include('partials.ws-ui')

@php
    $statusBadge = [
        'pending'   => ['Pending', 'ui-badge--warn'],
        'confirmed' => ['Confirmed', 'ui-badge--good'],
        'shipped'   => ['Shipped', 'ui-badge--good'],
        'delivered' => ['Delivered', 'ui-badge--good'],
        'cancelled' => ['Cancelled', 'ui-badge--bad'],
    ];

    $paymentBadge = [
        'pending'  => ['Payment pending', 'ui-badge--warn'],
        'paid'     => ['Paid', 'ui-badge--good'],
        'failed'   => ['Payment failed', 'ui-badge--bad'],
        'refunded' => ['Refunded (demo)', ''],
    ];

    [$statusText, $statusClass] = $statusBadge[$order->status] ?? [ucfirst($order->status), ''];

    if ($order->payment_method === 'cod' && $order->payment_status === 'pending') {
        [$payText, $payClass] = ['Pay on delivery', ''];
    } else {
        [$payText, $payClass] = $paymentBadge[$order->payment_status] ?? [ucfirst($order->payment_status), ''];
    }

    $canPay = $order->payment_method === 'online_demo'
        && $order->payment_status !== 'paid'
        && $order->status !== 'cancelled';

    $canCancel = in_array($order->status, ['pending', 'confirmed'], true);

    // Progress steps
    $steps = [
        'pending'   => 'Order placed',
        'confirmed' => 'Confirmed',
        'shipped'   => 'Shipped',
        'delivered' => 'Delivered',
    ];

    $stepKeys  = array_keys($steps);
    $stepIndex = array_search($order->status, $stepKeys, true);
@endphp

<style>
    .ord-steps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .ord-steps li {
        position: relative;
        padding-top: 30px;
        color: var(--u-muted);
        font-size: 0.82rem;
        font-weight: 700;
        text-align: center;
    }

    .ord-steps li::before {
        content: "";
        position: absolute;
        top: 6px;
        left: 50%;
        z-index: 1;
        width: 14px;
        height: 14px;
        border: 2px solid var(--u-border);
        border-radius: 50%;
        background: var(--u-toast);
        transform: translateX(-50%);
    }

    .ord-steps li::after {
        content: "";
        position: absolute;
        top: 12px;
        left: -50%;
        width: 100%;
        height: 2px;
        background: var(--u-border);
    }

    .ord-steps li:first-child::after {
        display: none;
    }

    .ord-steps li.is-done {
        color: var(--u-peach);
    }

    .ord-steps li.is-done::before {
        border-color: var(--u-pink);
        background: var(--u-pink);
    }

    .ord-steps li.is-done::after {
        background: var(--u-pink);
    }

    .ord-cancelled {
        padding: 14px 16px;
        border: 1px solid rgba(255, 120, 120, 0.5);
        border-radius: 12px;
        background: rgba(255, 90, 90, 0.12);
        color: #ffc4c4;
    }

    .ord-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ord-actions form {
        margin: 0;
    }

    .ord-actions .ui-btn {
        width: auto;
        min-height: 44px;
        padding: 10px 20px;
    }

    @media print {
        .acct-menu, .ord-actions, .ui-link { display: none !important; }
    }
</style>

<h1>Order {{ $order->order_number }}</h1>

<p class="ui-sub" style="margin-bottom: 14px;">
    Placed on {{ $order->created_at->format('d M Y, h:i A') }}
</p>

<div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 22px;">
    <span class="ui-badge {{ $statusClass }}">{{ $statusText }}</span>
    <span class="ui-badge {{ $payClass }}">{{ $payText }}</span>
</div>

{{-- Progress --}}
<div class="ui-card">
    @if ($order->status === 'cancelled')
        <div class="ord-cancelled">
            <strong>This order was cancelled.</strong>
            @if ($order->payment_status === 'refunded')
                <br>Your payment has been refunded (demo).
            @endif
        </div>
    @else
        <ol class="ord-steps">
            @foreach ($steps as $key => $label)
                <li class="{{ $stepIndex !== false && array_search($key, $stepKeys, true) <= $stepIndex ? 'is-done' : '' }}">
                    {{ $label }}
                </li>
            @endforeach
        </ol>
    @endif
</div>

{{-- Actions --}}
<div class="ord-actions" style="margin-bottom: 20px;">
    @if ($canPay)
        <a href="{{ route('orders.pay', $order) }}" class="ui-btn">Pay now</a>
    @endif

    <form method="POST" action="{{ route('orders.reorder', $order) }}">
        @csrf
        <button type="submit" class="ui-btn ui-btn--ghost">Order again</button>
    </form>

    <a href="{{ route('orders.invoice', $order) }}" class="ui-btn ui-btn--ghost" target="_blank" rel="noopener">View invoice</a>

    @if ($canCancel)
        <form method="POST" action="{{ route('orders.cancel', $order) }}"
              onsubmit="return confirm('Cancel order {{ $order->order_number }}? This cannot be undone.');">
            @csrf
            <button type="submit" class="ui-btn ui-btn--ghost" style="border-color: rgba(255,120,120,.6); color: #ffc4c4;">
                Cancel order
            </button>
        </form>
    @endif
</div>

<div class="ui-layout">

    <div>

        <div class="ui-card">
            <h2>Items</h2>

            @foreach ($order->items as $item)
                <div class="ui-line" style="grid-template-columns: minmax(0, 1fr) auto;">
                    <div>
                        <div class="ui-line-name">{{ $item->product_name }}</div>
                        <div class="ui-line-meta">
                            {{ $item->size ? 'Size ' . $item->size . ' · ' : '' }}
                            ₹{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                        </div>
                    </div>

                    <strong>₹{{ number_format($item->line_total, 2) }}</strong>
                </div>
            @endforeach

            <div class="ui-row" style="margin-top: 10px;">
                <span>Subtotal</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>

            <div class="ui-row">
                <span>Shipping</span>
                <span>
                    @if ($order->shipping_charge > 0)
                        ₹{{ number_format($order->shipping_charge, 2) }}
                    @else
                        Free
                    @endif
                </span>
            </div>

            @if ($order->gst_amount > 0)
                <div class="ui-row">
                    <span>GST ({{ rtrim(rtrim(number_format($order->gst_rate, 2), '0'), '.') }}%)</span>
                    <span>₹{{ number_format($order->gst_amount, 2) }}</span>
                </div>
            @endif

            <div class="ui-row total">
                <span>Total</span>
                <span>₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

    </div>

    <aside>

        <div class="ui-card">
            <h2>Delivery to</h2>

            <p style="margin: 0; line-height: 1.8;">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->address }}<br>
                {{ $order->city }} - {{ $order->pincode }}<br>
                {{ $order->customer_phone }}
                @if ($order->customer_email)
                    <br>{{ $order->customer_email }}
                @endif
            </p>

            @if ($order->notes)
                <p class="ui-sub" style="margin: 14px 0 0; font-size: 0.85rem;">
                    Your note: {{ $order->notes }}
                </p>
            @endif
        </div>

        <div class="ui-card">
            <h2>Payment</h2>

            <p style="margin: 0 0 4px;">
                {{ $order->payment_method === 'cod' ? 'Cash on delivery' : 'Online payment (demo)' }}
            </p>

            @if ($order->paid_at)
                <p class="ui-sub" style="margin: 0; font-size: 0.85rem;">
                    Paid on {{ $order->paid_at->format('d M Y, h:i A') }}
                </p>
            @endif
        </div>

        <a href="{{ route('orders.index') }}" class="ui-link">&larr; All my orders</a>

    </aside>

</div>

@endsection