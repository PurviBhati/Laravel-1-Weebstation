@extends('layouts.app')

@section('title', 'Orders | Admin')

@section('content')

@php
    $statusLabels = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'shipped'   => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    $tabs = [
        ''          => ['All', $totalCount],
        'pending'   => ['Pending', $counts['pending'] ?? 0],
        'confirmed' => ['Confirmed', $counts['confirmed'] ?? 0],
        'shipped'   => ['Shipped', $counts['shipped'] ?? 0],
        'delivered' => ['Delivered', $counts['delivered'] ?? 0],
        'cancelled' => ['Cancelled', $counts['cancelled'] ?? 0],
        'address'   => ['Address updated', $addressCount],
    ];
@endphp

<style>
    .o-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }

    .o-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: #fff;
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
    }

    .o-tab:hover {
        background: #f1f4f9;
    }

    .o-tab.is-active {
        border-color: #151c2c;
        background: #151c2c;
        color: #fff;
    }

    .o-tab .n {
        min-width: 20px;
        padding: 1px 6px;
        border-radius: 999px;
        background: #eef0f4;
        color: #536174;
        font-size: 11px;
        text-align: center;
    }

    .o-tab.is-active .n {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .o-tab.is-alert .n {
        background: #fff0c7;
        color: #8b5a00;
    }

    .o-search {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .o-search input {
        max-width: 340px;
    }

    .o-badge {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .o-badge.pending   { background: #fff0c7; color: #8b5a00; }
    .o-badge.confirmed { background: #dbeafe; color: #1e40af; }
    .o-badge.shipped   { background: #e0e7ff; color: #3730a3; }
    .o-badge.delivered { background: #d9f8e9; color: #087443; }
    .o-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .o-badge.flag      { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    .o-badge.neutral   { background: #eef0f4; color: #536174; }

    tr.is-cancelled td {
        background: #fff8f8;
    }

    .num {
        text-align: right;
    }

    .o-empty {
        padding: 30px;
        text-align: center;
        color: var(--muted);
    }
</style>

<div class="header">
    <div>
        <h1>Orders</h1>
        <p class="page-subtitle">Orders placed on the website.</p>
    </div>
</div>

<div class="o-tabs">
    @foreach ($tabs as $key => [$label, $count])
        <a
            href="{{ route('admin.orders.index', array_filter(['status' => $key, 'q' => $search])) }}"
            class="o-tab {{ (string) $status === (string) $key ? 'is-active' : '' }} {{ $key === 'address' && $count > 0 ? 'is-alert' : '' }}"
        >
            {{ $label }} <span class="n">{{ $count }}</span>
        </a>
    @endforeach
</div>

<div class="card">

    <form method="GET" action="{{ route('admin.orders.index') }}" class="o-search">
        @if ($status !== '')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif

        <input type="text" name="q" value="{{ $search }}" placeholder="Search order no., name or phone">
        <button type="submit" class="btn">Search</button>

        @if ($search !== '')
            <a href="{{ route('admin.orders.index', array_filter(['status' => $status])) }}" class="btn btn-light">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="num">Items</th>
                    <th class="num">Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @forelse ($orders as $order)
                    <tr class="{{ $order->status === 'cancelled' ? 'is-cancelled' : '' }}">
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            {{ $order->customer_name }}<br>
                            <span style="color: var(--muted);">{{ $order->customer_phone }}</span>
                        </td>
                        <td class="num">{{ $order->items_count }}</td>
                        <td class="num">₹{{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="o-badge neutral">
                                {{ $order->payment_method === 'cod' ? 'COD' : 'Online' }}:
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="o-badge {{ $order->status }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>

                            @if ($order->status === 'cancelled')
                                <br>
                                <span style="color: #991b1b; font-size: 12px;">
                                    by {{ $order->cancelled_by === 'admin' ? 'shop' : 'customer' }}
                                    @if ($order->cancelled_at)
                                        · {{ $order->cancelled_at->format('d M, h:i A') }}
                                    @endif
                                </span>
                            @endif

                            @if ($order->address_updated_at)
                                <br>
                                <span class="o-badge flag" style="margin-top: 4px;">
                                    Address updated
                                </span>
                                <br>
                                <small style="color: #92400e;">
                                    {{ $order->address_updated_at->format('d M, h:i A') }}
                                </small>
                            @endif
                        </td>
                        <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="o-empty">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $orders->links() }}
    </div>

</div>

@endsection