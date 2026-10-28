@extends('layouts.storefront')

@section('title', 'Payment | WEEB STATION   ')
@section('content')
@include('partials.ws-ui')

<div class="ui-page" style="width: min(520px, calc(100% - 32px));">
    <h1>Complete ypur Payment</h1>
    <p class="ui-sub">Order {{ $order->order_number }}</p>

    <div class="ui-card">
        <div style="margin-bottom: 18px; padding: 12px 14px; border: 1px dashed rgba(216, 110, 124, 0.7); border-radius: 12px; background: rgba(216, 110, 124, 0.1); font-size: 0.85rem; line-height: 1.6;">
            <strong>Demo payment.</strong>
            No real money is taken, Use the buttons below to pretend the payment worked or failed.
        </div>
        <div class="ui-row total" style="margin-top: 0; border-top: 0;">
            <span>Amount to pay</span>
            <span>₹{{ number_format($order->total, 2) }}</span>
        </div>

        @if ($order->payment_status === 'failed')
            <p class="ui-error" style="margin: 14px 0 0;">The last payment attempt failed. You can try again.</p>
        @endif

        <form method="POST" action="{{ route('orders.pay.process', $order) }}">
            @csrf
            <input type="hidden" name="result" value="success">
            <button type="submit" class="ui-btn" style="margin-top: 20px;">
                Pay ₹{{ number_format($order->total, 2) }} now
            </button>
        </form>

        <form method="POST" action="{{ route('orders.pay.process', $order) }}">
            @csrf
            <input type="hidden" name="result" value="cod">
            <button type="submit" class="ui-link">Pay on delivery instead</button>
        </form>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="ui-link">&larr; View order</a>
    </div>
</div>
@endsection