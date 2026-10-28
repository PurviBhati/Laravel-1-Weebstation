@extends('layouts.storefront')

@section('title', 'Your wishlist | WEEB STATION')

@section('content')

<style>
    .ws-wish {
        --w-peach: #ffd9bf;
        --w-pink: #d86e7c;
        --w-wine: #752027;
        --w-toast: #200b05;
        --w-muted: rgba(255, 217, 191, 0.62);
        --w-border: rgba(255, 217, 191, 0.16);

        width: min(1200px, calc(100% - 32px));
        margin: 0 auto;
        padding: 34px 0 60px;
        color: var(--w-peach);
    }

    .ws-wish *,
    .ws-wish *::before,
    .ws-wish *::after {
        box-sizing: border-box;
    }

    .ws-wish h1 {
        margin: 0 0 6px;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .ws-wish .wish-sub {
        margin: 0 0 28px;
        color: var(--w-muted);
        font-size: 0.95rem;
    }

    .ws-wish .wish-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 26px 20px;
    }

    .ws-wish .wish-card {
        position: relative;
        display: flex;
        min-width: 0;
        flex-direction: column;
    }

    .ws-wish .wish-image {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        aspect-ratio: 4 / 5;
        overflow: hidden;
        border: 1px solid var(--w-border);
        border-radius: 10px;
        background: linear-gradient(145deg, var(--w-wine), var(--w-toast));
        color: var(--w-peach);
        font-size: 2.4rem;
        font-weight: 900;
        letter-spacing: -3px;
        text-decoration: none;
    }

    .ws-wish .wish-image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .ws-wish .wish-card:hover .wish-image img {
        transform: scale(1.04);
    }

    .ws-wish .wish-image.is-sold img {
        opacity: 0.45;
    }

    .ws-wish .sold-tag {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 6px 9px;
        border-radius: 4px;
        background: #000;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .ws-wish .wish-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
        margin: 0;
    }

    .ws-wish .wish-remove button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid rgba(255, 217, 191, 0.4);
        border-radius: 50%;
        background: rgba(32, 11, 5, 0.8);
        color: var(--w-pink);
        cursor: pointer;
    }

    .ws-wish .wish-remove button:hover {
        background: var(--w-wine);
    }

    .ws-wish .wish-remove svg {
        width: 19px;
        height: 19px;
        fill: currentColor;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linejoin: round;
    }

    .ws-wish .wish-info {
        padding: 14px 2px 0;
    }

    .ws-wish .wish-category {
        margin: 0 0 6px;
        color: var(--w-muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .ws-wish .wish-name {
        display: block;
        margin: 0 0 8px;
        color: var(--w-peach);
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
        text-decoration: none;
    }

    .ws-wish .wish-name:hover {
        color: var(--w-pink);
    }

    .ws-wish .wish-price {
        font-size: 16px;
        font-weight: 800;
    }

    .ws-wish .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        margin-top: 22px;
        padding: 12px 22px;
        border: 1px solid var(--w-peach);
        border-radius: 8px;
        background: var(--w-peach);
        color: var(--w-toast);
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ws-wish .btn:hover {
        background: transparent;
        color: var(--w-peach);
    }

    .ws-wish .empty {
        padding: 60px 20px;
        border: 1px solid var(--w-border);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.04);
        text-align: center;
    }

    .ws-wish .empty h2 {
        margin: 0 0 10px;
        font-size: 1.6rem;
    }

    .ws-wish .empty p {
        max-width: 420px;
        margin: 0 auto;
        color: var(--w-muted);
        line-height: 1.7;
    }

    @media (max-width: 1000px) {
        .ws-wish .wish-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .ws-wish .wish-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px 12px;
        }

        .ws-wish .wish-name {
            font-size: 13px;
        }
    }
</style>

<div class="ws-wish">

    <h1>Your wishlist</h1>

    @if ($products->isEmpty())

        <div class="empty">
            <h2>Nothing saved yet.</h2>
            <p>
                Tap the heart on any product to save it here for later.
            </p>

            <a href="{{ url('/shop/everyone') }}" class="btn">Browse the collection</a>
        </div>

    @else

        <p class="wish-sub">
            {{ $products->count() }}
            {{ $products->count() === 1 ? 'piece' : 'pieces' }} saved
        </p>

        <div class="wish-grid">
            @foreach ($products as $product)

                @php
                    $image   = $product->images->first();
                    $isSold  = $product->stock <= 0;
                @endphp

                <div class="wish-card">

                    <form method="POST" action="{{ route('user.wishlist.toggle') }}" class="wish-remove">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <button type="submit" aria-label="Remove {{ $product->name }} from wishlist" title="Remove from wishlist">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </form>

                    <a
                        href="{{ url('/product/' . $product->id) }}"
                        class="wish-image {{ $isSold ? 'is-sold' : '' }}"
                    >
                        @if ($isSold)
                            <span class="sold-tag">Sold out</span>
                        @endif

                        @if ($image && $image->path)
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            WS.
                        @endif
                    </a>

                    <div class="wish-info">
                        <p class="wish-category">{{ $product->category->name ?? 'WEEB STATION' }}</p>

                        <a href="{{ url('/product/' . $product->id) }}" class="wish-name">
                            {{ $product->name }}
                        </a>

                        <span class="wish-price">₹{{ number_format((float) $product->price, 2) }}</span>
                    </div>

                </div>

            @endforeach
        </div>

    @endif

</div>

@endsection