@extends('layouts.storefront')
@include('partials.ws-ui')

@section('title', 'My account | WEEB STATION')

@section('account')

@php
    $statusBadge = [
        'pending'   => ['Pending', 'ui-badge--warn'],
        'confirmed' => ['Confirmed', 'ui-badge--good'],
        'shipped'   => ['Shipped', 'ui-badge--good'],
        'delivered' => ['Delivered', 'ui-badge--good'],
        'cancelled' => ['Cancelled', 'ui-badge--bad'],
    ];
@endphp

<style>
    .acct-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .acct-stat {
        padding: 18px;
        border: 1px solid var(--u-border);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.05);
    }

    .acct-stat strong {
        display: block;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .acct-stat span {
        color: var(--u-muted);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .acct-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .acct-head h2 {
        margin: 0;
    }

    .acct-head a {
        color: var(--u-pink);
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
    }

    @media (max-width: 600px) {
        .acct-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<h1>Hi, {{ \Illuminate\Support\Str::words($customer->name, 1, '') }}</h1>
<p class="ui-sub">Here is what's happening with your orders.</p>

<div class="acct-stats">
    <div class="acct-stat">
        <strong>{{ $orderCount }}</strong>
        <span>Orders placed</span>
    </div>

    <div class="acct-stat">
        <strong>{{ $activeCount }}</strong>
        <span>On the way</span>
    </div>

    <div class="acct-stat">
        <strong>{{ $wishCount }}</strong>
        <span>In wishlist</span>
    </div>
</div>
<div class="ui-card">
    <div class="acct-head">
        <h2>Recent orders</h2>

        @if ($orderCount > 0)
            <a href="{{ route('orders.index') }}">See all &rarr;</a>
        @endif
    </div>

    @if ($recent->isEmpty())
        <p class="ui-sub" style="margin: 0;">
            You haven't ordered anything yet.
            <a class="ui-link" href="{{ url('/shop/everyone') }}">Start shopping</a>
        </p>
    @else
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th class="num">Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($recent as $order)
                        @php [$text, $class] = $statusBadge[$order->status] ?? [ucfirst($order->status), '']; @endphp

                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td class="num">₹{{ number_format($order->total, 2) }}</td>
                            <td><span class="ui-badge {{ $class }}">{{ $text }}</span></td>
                            <td><a href="{{ route('orders.show', $order) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="ui-card">
    <div class="acct-head">
        <h2>Delivery address</h2>
        <a href="{{ route('account.profile') }}">{{ $customer->address ? 'Change' : 'Add' }} &rarr;</a>
    </div>

    @if ($customer->address)
        <p style="margin: 0; line-height: 1.8;">
            <strong>{{ $customer->name }}</strong><br>
            {{ $customer->address }}<br>
            {{ $customer->city }}@if ($customer->pincode) - {{ $customer->pincode }} @endif
        </p>
    @else
        <p class="ui-sub" style="margin: 0;">
            No address saved yet. Add one and checkout will fill it in for you.
        </p>
    @endif
</div>

@endsection