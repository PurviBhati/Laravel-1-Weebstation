
@extends('layouts.app')

@section('title', 'Orders')
@section('page-heading', 'Orders Management')

@section('content')
<style>
    .orders-page {
        max-width: 1500px;
        margin: 0 auto;
        padding: 26px;
        color: #182230;
    }

    .orders-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .orders-heading h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
    }

    .orders-heading p {
        margin: 7px 0 0;
        color: #718096;
        font-size: 14px;
    }

    .orders-count {
        padding: 9px 13px;
        border: 1px solid #e4e9f1;
        background: white;
        border-radius: 10px;
        font-size: 13px;
        color: #526078;
    }

    .orders-filters {
        background: #fff;
        border: 1px solid #e4e9f1;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: minmax(220px, 2fr) repeat(3, minmax(140px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .filter-field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #526078;
        margin-bottom: 7px;
    }

    .filter-field input,
    .filter-field select {
        width: 100%;
        min-width: 0;
        height: 42px;
        padding: 0 11px;
        background: #fff;
        border: 1px solid #dce2eb;
        border-radius: 8px;
        color: #182230;
        outline: none;
    }

    .filter-field input:focus,
    .filter-field select:focus {
        border-color: #315efb;
        box-shadow: 0 0 0 3px rgba(49, 94, 251, .1);
    }

    .filter-actions {
        display: flex;
        gap: 9px;
        margin-top: 14px;
        justify-content: flex-end;
    }

    .orders-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 39px;
        padding: 9px 15px;
        border: 1px solid #dce2eb;
        border-radius: 8px;
        background: #fff;
        color: #344054;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .orders-btn-primary {
        background: #182230;
        border-color: #182230;
        color: #fff;
    }

    .orders-table-card {
        background: #fff;
        border: 1px solid #e4e9f1;
        border-radius: 14px;
        overflow: hidden;
    }

    .orders-table-top {
        padding: 18px 20px;
        border-bottom: 1px solid #edf0f5;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
    }

    .orders-table-top h2 {
        margin: 0;
        font-size: 16px;
    }

    .orders-table-top span {
        color: #718096;
        font-size: 12px;
    }

    .orders-table-wrap {
        overflow-x: auto;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .orders-table th {
        padding: 13px 16px;
        text-align: left;
        background: #f8f9fc;
        color: #718096;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .orders-table td {
        padding: 16px;
        border-top: 1px solid #edf0f5;
        font-size: 13px;
        vertical-align: middle;
    }

    .orders-table tbody tr:hover {
        background: #fbfcff;
    }

    .order-number {
        font-weight: 700;
        color: #182230;
    }

    .customer-name {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .muted {
        color: #8792a5;
        font-size: 12px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-pending { background: #fff3d6; color: #8a5b00; }
    .status-confirmed, .status-paid { background: #dcfce7; color: #166534; }
    .status-shipped { background: #dbeafe; color: #1d4ed8; }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-failed, .status-cancelled { background: #fee2e2; color: #991b1b; }
    .status-processing { background: #ede9fe; color: #5b21b6; }
    .status-default { background: #edf0f5; color: #526078; }

    .view-order {
        display: inline-flex;
        padding: 8px 11px;
        border: 1px solid #dce2eb;
        border-radius: 7px;
        color: #344054;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .view-order:hover {
        background: #182230;
        color: #fff;
        border-color: #182230;
    }

    .orders-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .orders-empty h3 {
        margin: 0 0 7px;
        font-size: 17px;
    }

    .orders-empty p {
        color: #718096;
        margin: 0;
        font-size: 13px;
    }

    .orders-pagination {
        padding: 16px 20px;
        border-top: 1px solid #edf0f5;
    }

    @media (max-width: 1000px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .orders-page { padding: 16px; }
        .filter-grid { grid-template-columns: 1fr; }
        .filter-actions { justify-content: stretch; }
        .filter-actions .orders-btn { flex: 1; }
        .orders-heading h1 { font-size: 23px; }
    }
</style>

<div class="orders-page">
    <div class="orders-heading">
        <div>
            <h1>Orders</h1>
            <p>Track customer purchases, payments, and delivery status.</p>
        </div>

        <div class="orders-count">
            {{ $orders->total() }} total orders
        </div>
    </div>

    <form method="GET"
          action="{{ route('admin.orders.index') }}"
          class="orders-filters">

        <div class="filter-grid">
            <div class="filter-field">
                <label for="search">Search orders</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Order no., customer, email, phone"
                >
            </div>

            <div class="filter-field">
                <label for="status">Order status</label>
                <select id="status" name="status">
                    <option value="">All status</option>
                    @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="payment_status">Payment status</label>
                <select id="payment_status" name="payment_status">
                    <option value="">All payments</option>
                    @foreach (['pending', 'paid', 'failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(request('payment_status') === $paymentStatus)>
                            {{ ucfirst($paymentStatus) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="sort">Sort by</label>
                <select id="sort" name="sort">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                    <option value="amount_high" @selected(request('sort') === 'amount_high')>Highest amount</option>
                    <option value="amount_low" @selected(request('sort') === 'amount_low')>Lowest amount</option>
                    <option value="order_asc" @selected(request('sort') === 'order_asc')>Order number A–Z</option>
                    <option value="order_desc" @selected(request('sort') === 'order_desc')>Order number Z–A</option>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <a href="{{ route('admin.orders.index') }}" class="orders-btn">Clear filters</a>
            <button type="submit" class="orders-btn orders-btn-primary">Apply filters</button>
        </div>
    </form>

    <div class="orders-table-card">
        <div class="orders-table-top">
            <h2>All orders</h2>
            <span>Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</span>
        </div>

        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Order status</th>
                        <th>Items</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $orderStatus = strtolower($order->status ?? 'pending');
                            $paymentStatus = strtolower($order->payment_status ?? 'pending');

                            $statusClasses = [
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'processing' => 'status-processing',
                                'shipped' => 'status-shipped',
                                'delivered' => 'status-delivered',
                                'cancelled' => 'status-cancelled',
                                'paid' => 'status-paid',
                                'failed' => 'status-failed',
                            ];
                        @endphp

                        <tr>
                            <td>
                                <div class="order-number">
                                    #{{ $order->order_number ?? $order->id }}
                                </div>
                                <div class="muted">ID: {{ $order->id }}</div>
                            </td>

                            <td>
                                <div class="customer-name">
                                    {{ $order->customer_name ?? 'Customer' }}
                                </div>
                                <div class="muted">{{ $order->customer_email ?? 'No email' }}</div>
                                @if ($order->customer_phone)
                                    <div class="muted">{{ $order->customer_phone }}</div>
                                @endif
                            </td>

                            <td>
                                {{ $order->created_at?->format('d M Y') ?? '—' }}
                                <div class="muted">{{ $order->created_at?->format('h:i A') }}</div>
                            </td>

                            <td><strong>₹{{ number_format((float) $order->total, 2) }}</strong></td>

                            <td>
                                <div>{{ strtoupper(str_replace('_', ' ', $order->payment_method ?? '—')) }}</div>
                                <div style="margin-top:6px">
                                    <span class="status-pill {{ $statusClasses[$paymentStatus] ?? 'status-default' }}">
                                        {{ ucfirst($paymentStatus) }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span class="status-pill {{ $statusClasses[$orderStatus] ?? 'status-default' }}">
                                    {{ ucfirst($orderStatus) }}
                                </span>
                            </td>

                            <td>{{ $order->items_count ?? $order->items()->count() }}</td>

                            <td>
                                <a class="view-order"
                                   href="{{ route('admin.orders.show', $order) }}">
                                    View order →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="orders-empty">
                                    <h3>No orders found</h3>
                                    <p>Try changing your search or clearing the filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="orders-pagination">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
