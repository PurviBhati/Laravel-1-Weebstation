@extends('layouts.storefront')

@section('title', 'My orders | WEEB STATION')

@section('content')

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
        'pending' => ['Payment pending', 'ui-badge--warn'],
        'paid'    => ['Paid', 'ui-badge--good'],
        'failed'  => ['Payment failed', 'ui-badge--bad'],
    ];
@endphp

<div class="ui-page">

    <h1>My orders</h1>
    <p class="ui-sub">Everything you've ordered from WEEB STATION.</p>

    @if ($orders->isEmpty())

        <div class="ui-empty">
            <h2>No orders yet.</h2>
            <p>When you place an order, you'll find it here.</p>

            <a href="{{ url('/shop/everyone') }}" class="ui-btn ui-btn--auto">Start shopping</a>
        </div>

    @else

        <div class="ui-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th class="num">Items</th>
                            <th class="num">Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                [$statusText, $statusClass] = $statusBadge[$order->status] ?? [ucfirst($order->status), ''];

                                if ($order->payment_method === 'cod') {
                                    [$payText, $payClass] = ['Pay on delivery', ''];
                                } else {
                                    [$payText, $payClass] = $paymentBadge[$order->payment_status] ?? [ucfirst($order->payment_status), ''];
                                }
                            @endphp

                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td class="num">{{ $order->items_count }}</td>
                                <td class="num">₹{{ number_format($order->total, 2) }}</td>
                                <td><span class="ui-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td><span class="ui-badge {{ $payClass }}">{{ $payText }}</span></td>
                                <td><a href="{{ route('orders.show', $order) }}">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">
                {{ $orders->links() }}
            </div>
        </div>

    @endif

</div>

@endsection