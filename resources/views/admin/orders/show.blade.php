
@extends('layouts.app')

@section('title', 'Order Details')

@section('page-heading', 'Order Details')

@section('content')
@php
    $statusColors = [
        'pending' => '#f59e0b',
        'confirmed' => '#2563eb',
        'processing' => '#7c3aed',
        'shipped' => '#0891b2',
        'delivered' => '#16a34a',
        'cancelled' => '#dc2626',
    ];

    $orderStatus = strtolower($order->status ?? 'pending');
    $paymentStatus = strtolower($order->payment_status ?? 'pending');
@endphp

<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h2 style="margin:0 0 6px; font-size:24px;">
                Order #{{ $order->order_number ?? $order->id }}
            </h2>
            <p style="margin:0; color:#777; font-size:14px;">
                Placed {{ $order->created_at?->format('d M Y, h:i A') }}
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           style="display:inline-block; padding:10px 16px; border:1px solid #ddd; border-radius:8px; color:#222; text-decoration:none;">
            ← Back to Orders
        </a>
    </div>

    {{-- Order status --}}
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px;">
        <span style="padding:8px 13px; border-radius:20px; background:{{ $statusColors[$orderStatus] ?? '#777' }}18; color:{{ $statusColors[$orderStatus] ?? '#777' }}; font-size:13px; font-weight:600;">
            Order: {{ ucfirst($orderStatus) }}
        </span>

        <span style="padding:8px 13px; border-radius:20px; background:#f1f5f9; color:#334155; font-size:13px; font-weight:600;">
            Payment: {{ ucfirst($paymentStatus) }}
        </span>
    </div>

    <div style="display:grid; grid-template-columns: minmax(0, 1.6fr) minmax(260px, 1fr); gap:22px; align-items:start;">

        {{-- Ordered items --}}
        <section style="background:#fff; border:1px solid #e8e8e8; border-radius:14px; padding:22px;">
            <h3 style="margin:0 0 12px; font-size:18px;">Items Ordered</h3>

            @forelse ($order->items as $item)
                @php
                    $product = $item->product_id
                        ? \App\Models\Product::with('images')->find($item->product_id)
                        : null;

                    $imagePath = $product?->images?->first()?->path
                        ?? $product?->image;

                    $imageUrl = $imagePath
                        ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                            ? $imagePath
                            : \Illuminate\Support\Facades\Storage::url($imagePath))
                        : null;
                @endphp

                <div style="display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:14px; align-items:center; padding:16px 0; border-bottom:1px solid #eee;">
                    <div style="display:flex; align-items:center; gap:14px; min-width:0;">

                        <div style="width:78px; height:92px; flex-shrink:0; overflow:hidden; border-radius:8px; background:#f3f3f3;">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $item->product_name }}"
                                    style="width:100%; height:100%; object-fit:cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div style="display:none; height:100%; align-items:center; justify-content:center; color:#888; font-size:11px;">
                                    No image
                                </div>
                            @else
                                <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#888; font-size:11px;">
                                    No image
                                </div>
                            @endif
                        </div>

                        <div style="min-width:0;">
                            <div style="font-weight:600; color:#222; overflow-wrap:anywhere;">
                                {{ $item->product_name }}
                            </div>

                            <div style="font-size:13px; color:#777; margin-top:7px;">
                                @if ($item->size)
                                    Size: {{ $item->size }} ·
                                @endif
                                ₹{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                            </div>
                        </div>
                    </div>

                    <strong style="white-space:nowrap;">
                        ₹{{ number_format($item->line_total, 2) }}
                    </strong>
                </div>
            @empty
                <p style="color:#777; padding:20px 0;">No items were found for this order.</p>
            @endforelse

            <div style="display:flex; justify-content:space-between; gap:16px; padding-top:20px; font-size:18px;">
                <strong>Total</strong>
                <strong>₹{{ number_format($order->total ?? 0, 2) }}</strong>
            </div>
        </section>

        {{-- Customer and payment details --}}
        <div style="display:grid; gap:20px;">

            <section style="background:#fff; border:1px solid #e8e8e8; border-radius:14px; padding:22px;">
                <h3 style="margin:0 0 18px; font-size:18px;">Customer Details</h3>

                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Name</div>
                    <div style="font-weight:500; overflow-wrap:anywhere;">
                        {{ $order->customer_name ?? '—' }}
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Email</div>
                    <div style="overflow-wrap:anywhere;">
                        {{ $order->customer_email ?? '—' }}
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Phone</div>
                    <div>{{ $order->customer_phone ?? '—' }}</div>
                </div>

                <div>
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Delivery Address</div>
                    <div style="line-height:1.6; overflow-wrap:anywhere;">
                        {{ $order->address ?? '—' }}<br>
                        {{ $order->city ?? '' }}
                        {{ $order->pincode ?? '' }}
                    </div>
                </div>
            </section>

            <section style="background:#fff; border:1px solid #e8e8e8; border-radius:14px; padding:22px;">
                <h3 style="margin:0 0 18px; font-size:18px;">Payment Details</h3>

                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Payment Method</div>
                    <div>{{ strtoupper($order->payment_method ?? '—') }}</div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Payment Status</div>
                    <div style="font-weight:600;">{{ ucfirst($paymentStatus) }}</div>
                </div>

                <div>
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Subtotal</div>
                    <div>₹{{ number_format($order->subtotal ?? $order->total ?? 0, 2) }}</div>
                </div>

                @if ($order->paid_at)
                    <div style="margin-top:14px;">
                        <div style="font-size:12px; color:#888; margin-bottom:4px;">Paid At</div>
                        <div>{{ $order->paid_at->format('d M Y, h:i A') }}</div>
                    </div>
                @endif
            </section>

        </div>
    </div>
</div>

<style>
    @media (max-width: 760px) {
        .order-details-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
