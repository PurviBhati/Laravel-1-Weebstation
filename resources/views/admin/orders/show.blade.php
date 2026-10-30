@extends('layouts.app')

@section('title', $order->order_number . ' | Admin')

@section('content')

@php
    $statusLabels = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'shipped'   => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    $nextLabels = [
        'confirmed' => 'Confirm order',
        'shipped'   => 'Mark as shipped',
        'delivered' => 'Mark as delivered',
    ];

    $canCancel = ! in_array($order->status, ['cancelled', 'delivered'], true);
@endphp

<style>
    .o-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .o-badge.pending   { background: #fff0c7; color: #8b5a00; }
    .o-badge.confirmed { background: #dbeafe; color: #1e40af; }
    .o-badge.shipped   { background: #e0e7ff; color: #3730a3; }
    .o-badge.delivered { background: #d9f8e9; color: #087443; }
    .o-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .o-badge.neutral   { background: #eef0f4; color: #536174; }

    .o-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .o-card {
        margin-bottom: 16px;
    }

    .o-card h2 {
        margin-bottom: 14px;
        font-size: 16px;
    }

    .o-notice {
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.6;
    }

    .o-notice.cancelled {
        border: 1px solid #fecaca;
        background: #fee2e2;
        color: #991b1b;
    }

    .o-notice.address {
        border: 1px solid #fcd34d;
        background: #fef3c7;
        color: #92400e;
    }

    .o-notice form {
        display: inline;
        margin: 0;
    }

    .o-notice .old {
        display: block;
        margin-top: 6px;
        padding: 8px 10px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.6);
        color: #374151;
        font-size: 13px;
    }

    .o-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .o-actions form {
        margin: 0;
    }

    .o-actions .btn {
        width: 100%;
    }

    .o-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 7px 0;
        font-size: 14px;
    }

    .o-line.total {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 2px solid #151c2c;
        font-size: 17px;
        font-weight: 700;
    }

    .num {
        text-align: right;
    }

    @media (max-width: 900px) {
        .o-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="header">
    <div>
        <h1>Order {{ $order->order_number }}</h1>
        <p class="page-subtitle">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <span class="o-badge {{ $order->status }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-light">&larr; All orders</a>
    </div>
</div>

{{-- The customer cancelled (or the shop did) --}}
@if ($order->status === 'cancelled')
    <div class="o-notice cancelled">
        <strong>
            This order was cancelled by the {{ $order->cancelled_by === 'admin' ? 'shop' : 'customer' }}
            @if ($order->cancelled_at)
                on {{ $order->cancelled_at->format('d M Y, h:i A') }}.
            @endif
        </strong>
        <br>
        The stock has been put back.
        @if ($order->payment_status === 'refunded')
            The payment was marked as refunded, so remember to send the money back.
        @endif
    </div>
@endif

{{-- The customer changed the address --}}
@if ($order->address_updated_at)
    <div class="o-notice address">
        <strong>Address updated</strong>
        by the customer on
        {{ $order->address_updated_at->format('d M Y, h:i A') }}.

        <p style="margin: 6px 0 0;">
            Please ship to the delivery address shown below.
        </p>
    </div>
@endif

<div class="o-grid">

    <div>

        <div class="card o-card">
            <h2>Items</h2>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Size</th>
                            <th class="num">Price</th>
                            <th class="num">Qty</th>
                            <th class="num">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->size ?? '-' }}</td>
                                <td class="num">₹{{ number_format($item->price, 2) }}</td>
                                <td class="num">{{ $item->quantity }}</td>
                                <td class="num">₹{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="max-width: 320px; margin: 16px 0 0 auto;">
                <div class="o-line">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </div>

                <div class="o-line">
                    <span>Shipping</span>
                    <span>{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'Free' }}</span>
                </div>

                @if ($order->gst_amount > 0)
                    <div class="o-line">
                        <span>GST ({{ rtrim(rtrim(number_format($order->gst_rate, 2), '0'), '.') }}%)</span>
                        <span>₹{{ number_format($order->gst_amount, 2) }}</span>
                    </div>
                @endif

                <div class="o-line total">
                    <span>Total</span>
                    <span>₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <aside>

        <div class="card o-card">
            <h2>Actions</h2>

            <div class="o-actions">
                @if ($nextStatus)
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                        <button type="submit" class="btn">{{ $nextLabels[$nextStatus] }}</button>
                    </form>
                @endif

                @if ($canCancel)
                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                          onsubmit="return confirm('Cancel order {{ $order->order_number }}? Its stock will be put back.');">
                        @csrf
                        <button type="submit" class="btn btn-danger">Cancel order</button>
                    </form>
                @endif

                @if (! $nextStatus && ! $canCancel)
                    <p style="margin: 0; color: var(--muted); font-size: 14px;">
                        This order is finished. No more actions.
                    </p>
                @endif
            </div>
        </div>

        <div class="card o-card">
            <h2>Customer</h2>

            <p style="margin: 0; line-height: 1.8; font-size: 14px;">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->customer_phone }}<br>
                {{ $order->customer_email ?: 'No email' }}
            </p>
        </div>

        <div class="card o-card">
            <h2>Deliver to</h2>

            <p style="margin: 0; line-height: 1.8; font-size: 14px;">
                {{ $order->address }}<br>
                {{ $order->city }} - {{ $order->pincode }}
            </p>

            @if ($order->notes)
                <p style="margin: 12px 0 0; color: var(--muted); font-size: 13px;">
                    Customer note: {{ $order->notes }}
                </p>
            @endif
        </div>

        <div class="card o-card">
            <h2>Payment</h2>

            <p style="margin: 0; line-height: 1.8; font-size: 14px;">
                {{ $order->payment_method === 'cod' ? 'Cash on delivery' : 'Online payment (demo)' }}<br>
                Status: <strong>{{ ucfirst($order->payment_status) }}</strong>
                @if ($order->paid_at)
                    <br>Paid on {{ $order->paid_at->format('d M Y, h:i A') }}
                @endif
            </p>
        </div>

    </aside>

</div>

@endsection