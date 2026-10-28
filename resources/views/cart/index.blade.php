@extends('layouts.storefront')

@section('title', 'Your cart | WEEB STATION')

@section('content')

@php
    $checkoutReady = \Illuminate\Support\Facades\Route::has('checkout.index');
@endphp

<style>
    .ws-cart {
        --c-peach: #ffd9bf;
        --c-pink: #d86e7c;
        --c-wine: #c9545e;
        --c-rum: #d4a3a7;
        --c-toast: #200505;
        --c-muted: rgba(255, 217, 191, 0.62);
        --c-border: rgba(187, 141, 111, 0.47);

        width: min(1100px, calc(100% - 32px));
        margin: 0 auto;
        padding: 34px 0 60px;
        color: var(--c-peach);
    }

    .ws-cart *,
    .ws-cart *::before,
    .ws-cart *::after {
        box-sizing: border-box;
    }

    .ws-cart h1 {
        margin: 0 0 6px;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .ws-cart .cart-sub {
        margin: 0 0 28px;
        color: var(--c-rum);
        font-size: 0.95rem;
    }

    .ws-cart .cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 28px;
        align-items: start;
    }

    .ws-cart .cart-item {
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr) auto;
        gap: 18px;
        padding: 18px;
        margin-bottom: 14px;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.04);
    }

    .ws-cart .cart-item.has-problem {
        border-color: rgba(255, 120, 120, 0.5);
    }

    .ws-cart .cart-thumb {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 96px;
        height: 120px;
        overflow: hidden;
        border-radius: 10px;
        background: linear-gradient(145deg, var(--c-wine), var(--c-toast));
        color: var(--c-rum);
        font-weight: 900;
        letter-spacing: -2px;
    }

    .ws-cart .cart-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ws-cart .cart-info {
        min-width: 0;
    }

    .ws-cart .cart-name {
        display: block;
        margin: 0 0 6px;
        color: var(--c-rum);
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
        text-decoration: none;
    }

    .ws-cart .cart-name:hover {
        color: var(--c-pink);
    }

    .ws-cart .cart-meta {
        margin: 0 0 12px;
        color: var(--c-rum);
        font-size: 0.85rem;
    }

    .ws-cart .cart-problem {
        margin: 0 0 12px;
        color: #ff0000;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .ws-cart .cart-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .ws-cart .cart-controls form {
        margin: 0;
    }

    .ws-cart .qty-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ws-cart .qty-form label {
        color: var(--c-rum);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ws-cart .qty-form input {
        width: 72px;
        height: 40px;
        padding: 0 10px;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 8px;
        background: rgba(32, 11, 5, 0.6);
        color: var(--c-peach);
        font: inherit;
        font-weight: 700;
        text-align: center;
    }

    .ws-cart .link-button {
        padding: 0;
        border: 0;
        background: none;
        color: var(--c-toast);
        font: inherit;
        font-size: 0.85rem;
        text-decoration: underline;
        cursor: pointer;
    }

    .ws-cart .link-button:hover {
        color: var(--c-pink);
    }

    .ws-cart .cart-price {
        text-align: right;
        white-space: nowrap;
    }

    .ws-cart .cart-price strong {
        display: block;
        font-size: 1.1rem;
    }

    .ws-cart .cart-price span {
        color: var(--c-rum);
        font-size: 0.8rem;
    }


    .ws-cart .summary {
        position: sticky;
        top: 90px;
        padding: 22px;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.05);
    }

    .ws-cart .summary h2 {
        margin: 0 0 16px;
        font-size: 1.1rem;
    }

    .ws-cart .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        color: var(--c-rum);
        font-size: 0.92rem;
    }

    .ws-cart .summary-row.total {
        margin-top: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--c-border);
        color: var(--c-rum);
        font-size: 1.15rem;
        font-weight: 800;
    }

    .ws-cart .btn {
        display: flex;
        width: 100%;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        margin-top: 18px;
        padding: 14px 20px;
        border: 1px solid var(--c-peach);
        border-radius: 10px;
        background: var(--c-wine);
        color: var(--c-toast);
        font: inherit;
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ws-cart .btn:hover:not(.is-off) {
        background: transparent;
        color: var(--c-peach);
    }

    .ws-cart .btn.is-off {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .ws-cart .btn-ghost {
        background: transparent;
        color: var(--c-peach);
    }

    .ws-cart .btn-ghost:hover {
        background: var(--c-peach);
        color: var(--c-toast);
    }

    .ws-cart .summary-note {
        margin: 12px 0 0;
        color: var(--c-toast);
        font-size: 0.8rem;
        line-height: 1.6;
        text-align: center;
    }
    .ws-cart .empty {
        padding: 60px 20px;
        border: 1px solid var(--c-wine);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.04);
        text-align: center;
    }

    .ws-cart .empty h2 {
        margin: 0 0 10px;
        font-size: 1.6rem;
    }

    .ws-cart .empty p {
        max-width: 420px;
        margin: 0 auto 6px;
        color: var(--c-muted);
        line-height: 1.7;
    }

    .ws-cart .empty .btn {
        display: inline-flex;
        width: auto;
        margin-top: 22px;
    }

    @media (max-width: 860px) {
        .ws-cart .cart-layout {
            grid-template-columns: 1fr;
        }

        .ws-cart .summary {
            position: static;
        }
    }

    @media (max-width: 520px) {
        .ws-cart .cart-item {
            grid-template-columns: 78px minmax(0, 1fr);
        }

        .ws-cart .cart-thumb {
            width: 78px;
            height: 98px;
        }

        .ws-cart .cart-price {
            grid-column: 1 / -1;
            text-align: left;
        }
    }
</style>

<div class="ws-cart">

    <h1 style="color: var(--c-rum)">Your cart</h1>

    @if (count($lines) === 0)

        <div class="empty">
            <h2>Your cart is empty.</h2>
            <p>Nothing here yet. Find a fit you love and it will show up here.</p>

            <a href="{{ url('/shop/everyone') }}" class="btn">Start shopping</a>
        </div>

    @else

        <p class="cart-sub">
            {{ collect($lines)->where('missing', false)->sum('quantity') }}
            {{ collect($lines)->where('missing', false)->sum('quantity') === 1 ? 'item' : 'items' }}
            in your cart
        </p>

        <div class="cart-layout">

            {{-- Items --}}
            <div>
                @foreach ($lines as $line)

                    @if ($line['missing'])
                        <div class="cart-item has-problem">
                            <div class="cart-thumb">WS.</div>

                            <div class="cart-info">
                                <p class="cart-name">This item is no longer available.</p>
                                <p class="cart-problem">Please remove it to continue.</p>

                                <div class="cart-controls">
                                    <form method="POST" action="{{ route('user.cart.remove', $line['key']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-button">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @continue
                    @endif

                    <div class="cart-item {{ $line['problem'] ? 'has-problem' : '' }}">

                        <a href="{{ url('/product/' . $line['product']->id) }}" class="cart-thumb">
                            @if ($line['image'])
                                <img
                                    src="{{ asset('storage/' . $line['image']) }}"
                                    alt="{{ $line['product']->name }}"
                                >
                            @else
                                WS.
                            @endif
                        </a>

                        <div class="cart-info">
                            <a href="{{ url('/product/' . $line['product']->id) }}" class="cart-name">
                                {{ $line['product']->name }}
                            </a>

                            <p class="cart-meta">
                                @if ($line['size'])
                                    Size: {{ $line['size'] }}
                                @else
                                    One size
                                @endif
                            </p>

                            @if ($line['problem'])
                                <p class="cart-problem">{{ $line['problem'] }}</p>
                            @endif

                            <div class="cart-controls">
                                <form
                                    method="POST"
                                    action="{{ route('user.cart.update', $line['key']) }}"
                                    class="qty-form"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <label for="qty-{{ $line['key'] }}">Qty</label>

                                    <input
                                        type="number"
                                        name="quantity"
                                        id="qty-{{ $line['key'] }}"
                                        value="{{ $line['quantity'] }}"
                                        min="1"
                                        max="{{ max($line['available'], 1) }}"
                                        inputmode="numeric"
                                        onchange="this.form.submit()"
                                    >

                                    <noscript>
                                        <button type="submit" class="link-button">Update</button>
                                    </noscript>
                                </form>

                                <form method="POST" action="{{ route('user.cart.remove', $line['key']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-button">Remove</button>
                                </form>
                            </div>
                        </div>

                        <div class="cart-price">
                            <strong style="color:var(--c-rum);">₹{{ number_format($line['total'], 2) }}</strong>
                            <span>₹{{ number_format($line['price'], 2) }} each</span>
                        </div>

                    </div>

                @endforeach

                <a href="{{ url('/shop/everyone') }}" class="link-button">&larr; Continue shopping</a>
            </div>

            {{-- Summary --}}
            <aside class="summary">
                <h2 style="color: var(--c-rum);">Order summary</h2>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>₹{{ number_format($subtotal, 2) }}</span>
                </div>

                @if ($hasProblem)
                    <span class="btn is-off" aria-disabled="true">Fix the items marked in red</span>
                @elseif ($checkoutReady)
                    <a href="{{ route('checkout.index') }}" class="btn">Proceed to checkout</a>
                @else
                    <span class="btn is-off" aria-disabled="true">Checkout: coming next</span>
                @endif

                <p class="summary-note">
                    Your cart is saved on this device for a while. Stock is checked again at checkout.
                </p>
            </aside>

        </div>

    @endif

</div>

@endsection