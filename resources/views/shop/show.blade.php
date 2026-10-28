@extends('layouts.storefront')

@section('title', $product->name . ' | WEEB STATION')

@section('content')

@php
    $productImages = $product->images;

    $sizes    = $product->variants->sortBy('id')->values();
    $hasSizes = $sizes->isNotEmpty();
    $soldOut  = $product->stock <= 0;

    $cartReady = \Illuminate\Support\Facades\Route::has('user.cart.add');

    $wishReady  = \Illuminate\Support\Facades\Route::has('user.wishlist.toggle');
    $wishlisted = in_array($product->id, \App\Support\WishlistStore::get());
@endphp

<style>
    .ws-product-detail {
        --detail-peach: #ffd9bf;
        --detail-pink: #d86e7c;
        --detail-wine: #752027;
        --detail-rum: #4a0a0f;
        --detail-dark: #200b05;

        max-width: 1200px;
        margin: 0 auto;
        padding: 28px 24px 70px;
        color: var(--detail-peach);
    }

    .ws-product-detail *,
    .ws-product-detail *::before,
    .ws-product-detail *::after {
        box-sizing: border-box;
    }

    .ws-product-detail .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 28px;
        color: var(--detail-peach);
        text-decoration: none;
        font-size: 0.92rem;
        transition: color 0.2s ease;
    }

    .ws-product-detail .back-link:hover {
        color: var(--detail-pink);
    }

    .ws-product-detail .detail {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 0.9fr);
        gap: clamp(28px, 5vw, 64px);
        align-items: start;
    }

    /* ---------- Image gallery ---------- */

    .ws-product-detail .detail-gallery {
        min-width: 0;
        width: 100%;
    }

    .ws-product-detail .detail-image {
        position: relative;
        width: 100%;
        overflow: hidden;
        border: 1px solid rgba(255, 217, 191, 0.15);
        border-radius: 20px;
        background: rgba(255, 217, 191, 0.04);
        touch-action: pan-y;
    }

    .ws-product-detail .gallery-track {
        display: flex;
        width: 100%;
        transition: transform 0.3s ease;
        will-change: transform;
    }

    .ws-product-detail .gallery-slide {
        flex: 0 0 100%;
        min-width: 0;
    }

    .ws-product-detail .gallery-slide img {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        user-select: none;
    }

    .ws-product-detail .no-image {
        display: flex;
        width: 100%;
        aspect-ratio: 4 / 5;
        align-items: center;
        justify-content: center;
        color: rgba(255, 217, 191, 0.6);
        font-size: 0.95rem;
        background: rgba(255, 217, 191, 0.05);
    }

    .ws-product-detail .gallery-arrow {
        position: absolute;
        top: 50%;
        z-index: 2;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(255, 217, 191, 0.4);
        border-radius: 50%;
        color: var(--detail-peach);
        background: rgba(32, 11, 5, 0.78);
        cursor: pointer;
        transform: translateY(-50%);
    }

    .ws-product-detail .gallery-prev {
        left: 12px;
    }

    .ws-product-detail .gallery-next {
        right: 12px;
    }

    .ws-product-detail .gallery-arrow:hover {
        background: var(--detail-wine);
    }

    .ws-product-detail .gallery-counter {
        position: absolute;
        right: 12px;
        bottom: 12px;
        padding: 6px 10px;
        border-radius: 20px;
        color: var(--detail-peach);
        background: rgba(32, 11, 5, 0.78);
        font-size: 0.8rem;
    }

    .ws-product-detail .gallery-thumbnails {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        overflow-x: auto;
        padding-bottom: 5px;
    }

    .ws-product-detail .gallery-thumb {
        flex: 0 0 76px;
        padding: 0;
        overflow: hidden;
        border: 2px solid transparent;
        border-radius: 9px;
        background: transparent;
        cursor: pointer;
        opacity: 0.65;
    }

    .ws-product-detail .gallery-thumb.active {
        border-color: var(--detail-pink);
        opacity: 1;
    }

    .ws-product-detail .gallery-thumb img {
        display: block;
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
    }

    /* ---------- Product information ---------- */

    .ws-product-detail .detail-info {
        padding-top: 10px;
        min-width: 0;
    }

    .ws-product-detail .detail-cat {
        margin: 0 0 14px;
        color: var(--detail-pink);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .ws-product-detail .detail h1 {
        margin: 0 0 18px;
        color: var(--detail-peach);
        font-size: clamp(2rem, 4vw, 3.2rem);
        font-weight: 700;
        letter-spacing: -0.04em;
        line-height: 1.12;
        overflow-wrap: anywhere;
    }

    .ws-product-detail .detail-price {
        margin: 0 0 18px;
        color: var(--detail-peach);
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        font-weight: 700;
    }

    .ws-product-detail .detail-stock {
        display: inline-flex;
        align-items: center;
        margin: 0 0 24px;
        padding: 8px 13px;
        border: 1px solid rgba(255, 217, 191, 0.18);
        border-radius: 999px;
        color: var(--detail-peach);
        background: rgba(255, 217, 191, 0.05);
        font-size: 0.82rem;
        font-weight: 600;
    }

    .ws-product-detail .detail-stock.low {
        border-color: rgba(216, 110, 124, 0.5);
        background: rgba(216, 110, 124, 0.1);
    }

    .ws-product-detail .detail-stock.out {
        color: #ffb5b5;
        border-color: rgba(255, 100, 100, 0.35);
        background: rgba(255, 100, 100, 0.08);
    }

    .ws-product-detail .detail-desc {
        margin: 0 0 30px;
        color: rgba(255, 217, 191, 0.78);
        font-size: 0.98rem;
        line-height: 1.85;
        overflow-wrap: anywhere;
    }

    .ws-product-detail .detail-divider {
        height: 1px;
        margin: 26px 0;
        border: 0;
        background: rgba(255, 217, 191, 0.15);
    }

    .ws-product-detail .detail-label {
        margin: 0 0 10px;
        color: rgba(255, 217, 191, 0.55);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    /* ---------- Size picker ---------- */

    .ws-product-detail .size-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 0 10px;
    }

    .ws-product-detail .size-option {
        position: relative;
    }

    .ws-product-detail .size-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ws-product-detail .size-option span {
        display: inline-flex;
        min-width: 54px;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 8px;
        color: var(--detail-peach);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .ws-product-detail .size-option span:hover {
        border-color: var(--detail-peach);
    }

    .ws-product-detail .size-option input:checked + span {
        border-color: var(--detail-peach);
        background: var(--detail-peach);
        color: var(--detail-dark);
    }

    .ws-product-detail .size-option input:focus-visible + span {
        outline: 2px solid var(--detail-pink);
        outline-offset: 3px;
    }

    .ws-product-detail .size-option.is-out span {
        opacity: 0.35;
        text-decoration: line-through;
        cursor: not-allowed;
    }

    .ws-product-detail .size-hint {
        min-height: 22px;
        margin: 0 0 20px;
        color: rgba(255, 217, 191, 0.7);
        font-size: 0.85rem;
    }

    .ws-product-detail .size-hint.is-error {
        color: #ffb5b5;
    }

    /* ---------- Quantity ---------- */

    .ws-product-detail .qty-box {
        display: inline-flex;
        align-items: stretch;
        margin: 0 0 22px;
        overflow: hidden;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 8px;
    }

    .ws-product-detail .qty-box button {
        width: 44px;
        border: 0;
        color: var(--detail-peach);
        background: rgba(255, 217, 191, 0.06);
        font-size: 1.2rem;
        cursor: pointer;
    }

    .ws-product-detail .qty-box button:hover {
        background: rgba(255, 217, 191, 0.14);
    }

    .ws-product-detail .qty-box input {
        width: 58px;
        height: 44px;
        border: 0;
        border-inline: 1px solid rgba(255, 217, 191, 0.3);
        color: var(--detail-peach);
        background: transparent;
        font: inherit;
        font-weight: 700;
        text-align: center;
        -moz-appearance: textfield;
    }

    .ws-product-detail .qty-box input::-webkit-outer-spin-button,
    .ws-product-detail .qty-box input::-webkit-inner-spin-button {
        margin: 0;
        -webkit-appearance: none;
    }

    /* ---------- Add to cart ---------- */

    .ws-product-detail .btn {
        display: inline-flex;
        width: 100%;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        padding: 14px 22px;
        border: 1px solid var(--detail-pink);
        border-radius: 10px;
        color: var(--detail-dark);
        background: var(--detail-pink);
        font: inherit;
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .ws-product-detail .btn:hover:not(:disabled) {
        background: var(--detail-peach);
        transform: translateY(-1px);
    }

    .ws-product-detail .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .ws-product-detail .detail-note {
        margin: 12px 0 0;
        color: rgba(255, 217, 191, 0.5);
        font-size: 0.78rem;
        line-height: 1.6;
        text-align: center;
    }

    /* ---------- Title row and wishlist heart ---------- */

    .ws-product-detail .title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin: 0 0 18px;
    }

    .ws-product-detail .title-row h1 {
        margin: 0;
    }

    .ws-product-detail .title-row form {
        margin: 0;
        flex: 0 0 auto;
    }

    .ws-product-detail .wish-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        padding: 0;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 50%;
        background: transparent;
        color: var(--detail-peach);
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    .ws-product-detail .wish-btn svg {
        width: 22px;
        height: 22px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linejoin: round;
    }

    .ws-product-detail .wish-btn:hover:not(:disabled) {
        border-color: var(--detail-pink);
        color: var(--detail-pink);
    }

    .ws-product-detail .wish-btn.is-active {
        border-color: var(--detail-pink);
        color: var(--detail-pink);
    }

    .ws-product-detail .wish-btn.is-active svg {
        fill: currentColor;
    }

    .ws-product-detail .wish-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ---------- Responsive ---------- */

    @media (max-width: 760px) {
        .ws-product-detail {
            padding: 20px 16px 48px;
        }

        .ws-product-detail .back-link {
            margin-bottom: 20px;
        }

        .ws-product-detail .detail {
            grid-template-columns: 1fr;
            gap: 26px;
        }

        .ws-product-detail .gallery-slide img,
        .ws-product-detail .no-image {
            aspect-ratio: 4 / 3;
        }

        .ws-product-detail .detail-info {
            padding-top: 0;
        }

        .ws-product-detail .detail h1 {
            font-size: clamp(1.8rem, 7vw, 2.5rem);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ws-product-detail .gallery-track {
            transition: none;
        }
    }
</style>

<div class="ws-product-detail">

    <a style="color: #4a0a0f;" class="back-link" href="{{ url('/shop/categories/' . (in_array($product->for_whom, ['men', 'women', 'everyone'], true) ? $product->for_whom : 'everyone')) }}">
        <span aria-hidden="true">←</span>Back to the shop
    </a>

    <div class="detail">
        <div class="detail-gallery">
            @if ($productImages->isNotEmpty())
                <div class="detail-image" id="galleryArea">
                    <div class="gallery-track" id="galleryTrack">
                        @foreach ($productImages as $image)
                            <div class="gallery-slide">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }} - Image {{ $loop->iteration }}" draggable="false" >
                            </div>
                        @endforeach
                    </div>

                    @if ($productImages->count() > 1)
                        <button type="button" class="gallery-arrow gallery-prev" id="galleryPrev" aria-label="Previous image">&#10094;</button>

                        <button type="button" class="gallery-arrow gallery-next" id="galleryNext" aria-label="Next image">&#10095;</button>

                        <span class="gallery-counter" id="galleryCounter">
                            1 / {{ $productImages->count() }}
                        </span>
                    @endif
                </div>

                @if ($productImages->count() > 1)
                    <div class="gallery-thumbnails">
                        @foreach ($productImages as $image)
                            <button type="button" class="gallery-thumb {{ $loop->first ? 'active' : '' }}" data-index="{{ $loop->index }}" aria-label="View image {{ $loop->iteration }}">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="detail-image">
                    <div class="no-image">Image coming soon</div>
                </div>
            @endif
        </div>
        <div class="detail-info">

            <p class="detail-cat">
                {{ $product->category->name ?? 'WEEB STATION' }}
                <span aria-hidden="true"> · </span>
                {{ ucfirst($product->for_whom ?? 'everyone') }}
            </p>

            <div class="title-row">
                <h1>{{ $product->name }}</h1>

                <form method="POST" action="{{ $wishReady ? route('user.wishlist.toggle') : '#' }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <button type="submit" class="wish-btn {{ $wishlisted ? 'is-active' : '' }}" aria-label="{{ $wishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}" title="{{ $wishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}" {{ $wishReady ? '' : 'disabled' }}>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <p class="detail-price">
                ₹{{ number_format($product->price, 2) }}
            </p>

            @if ($soldOut)
                <p class="detail-stock out">Sold out</p>
            @elseif ($product->stock <= 5)
                <p class="detail-stock low">Only {{ $product->stock }} left</p>
            @else
                <p class="detail-stock">In stock</p>
            @endif

            @if ($product->description)
                <p class="detail-desc">{!! nl2br(e($product->description)) !!}</p>
            @else
                <p class="detail-desc">
                    Discover your next favourite from WEEB STATION.
                </p>
            @endif

            <hr class="detail-divider">

            <form id="addToCartForm" method="POST" action="{{ $cartReady ? route('user.cart.add') : '#' }}" data-has-sizes="{{ $hasSizes ? '1' : '0' }}" data-stock="{{ max((int) $product->stock, 0) }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if ($hasSizes)
                    <p class="detail-label">Select size</p>

                    <div class="size-options" role="radiogroup" aria-label="Size">
                        @foreach ($sizes as $size)
                            @php $left = (int) $size->pivot->stock; @endphp

                            <label class="size-option {{ $left <= 0 ? 'is-out' : '' }}">
                                <input type="radio" name="variant_id" value="{{ $size->id }}" data-stock="{{ $left }}" {{ $left <= 0 ? 'disabled' : '' }}>
                                <span>{{ $size->value }}</span>
                            </label>
                        @endforeach
                    </div>

                    <p class="size-hint" id="sizeHint">Please choose a size.</p>
                @endif

                @unless ($soldOut)
                    <p class="detail-label">Quantity</p>

                    <div class="qty-box">
                        <button type="button" id="qtyMinus" aria-label="Decrease quantity">−</button>
                        <input type="number" name="quantity" id="qty" value="1"  min="1" max="{{ $hasSizes ? 1 : max((int) $product->stock, 1) }}" inputmode="numeric">
                        <button type="button" id="qtyPlus" aria-label="Increase quantity">+</button>
                    </div>
                @endunless

                <button type="submit" class="btn" id="addToCartBtn" {{ ($soldOut || ! $cartReady) ? 'disabled' : '' }}>
                    @if ($soldOut)
                        Sold out
                    @elseif (! $cartReady)
                        Choose Size First !!
                    @else
                        Add to cart
                    @endif
                </button>
            </form>

            <p class="detail-note">
                Secure checkout. Pay on delivery or online.
            </p>

        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const track = document.getElementById('galleryTrack');

    if (track && track.children.length > 1) {
        const total   = track.children.length;
        const area    = document.getElementById('galleryArea');
        const prev    = document.getElementById('galleryPrev');
        const next    = document.getElementById('galleryNext');
        const counter = document.getElementById('galleryCounter');
        const thumbs  = document.querySelectorAll('.gallery-thumb');

        let index  = 0;
        let startX = null;

        function go(target) {
            index = (target + total) % total;

            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            counter.textContent   = (index + 1) + ' / ' + total;

            thumbs.forEach(function (thumb, i) {
                thumb.classList.toggle('active', i === index);
            });

            if (thumbs[index]) {
                thumbs[index].scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        }

        prev.addEventListener('click', function () { go(index - 1); });
        next.addEventListener('click', function () { go(index + 1); });

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                go(Number(thumb.dataset.index));
            });
        });

        // Swipe on phones
        area.addEventListener('touchstart', function (event) {
            startX = event.touches[0].clientX;
        }, { passive: true });

        area.addEventListener('touchend', function (event) {
            if (startX === null) return;

            const distance = event.changedTouches[0].clientX - startX;

            if (Math.abs(distance) > 40) {
                go(index + (distance < 0 ? 1 : -1));
            }

            startX = null;
        });

        // Arrow keys (not while typing in a field)
        document.addEventListener('keydown', function (event) {
            const tag = (event.target.tagName || '').toLowerCase();

            if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

            if (event.key === 'ArrowLeft')  go(index - 1);
            if (event.key === 'ArrowRight') go(index + 1);
        });
    }

    const form = document.getElementById('addToCartForm');

    if (form) {
        const hasSizes = form.dataset.hasSizes === '1';
        const baseStock = Number(form.dataset.stock || 0);
        const qty = document.getElementById('qty');
        const hint = document.getElementById('sizeHint');

        function chosenSize() {
            return form.querySelector('input[name="variant_id"]:checked');
        }

        function currentMax() {
            if (hasSizes) {
                const chosen = chosenSize();
                return chosen ? Number(chosen.dataset.stock) : 1;
            }

            return Math.max(baseStock, 1);
        }

        function clampQty() {
            if (!qty) return;

            const max = currentMax();
            let value = parseInt(qty.value, 10);

            if (isNaN(value) || value < 1) value = 1;
            if (value > max) value = max;

            qty.max   = max;
            qty.value = value;
        }

        function updateHint() {
            if (!hint) return;

            hint.classList.remove('is-error');

            const chosen = chosenSize();

            if (!chosen) {
                hint.textContent = 'Please choose a size.';
                return;
            }

            const left = Number(chosen.dataset.stock);

            hint.textContent = left <= 5
                ? 'Only ' + left + ' left in this size.'
                : 'In stock.';
        }

        form.querySelectorAll('input[name="variant_id"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                updateHint();
                clampQty();
            });
        });

        const minus = document.getElementById('qtyMinus');
        const plus  = document.getElementById('qtyPlus');

        if (minus && plus && qty) {
            minus.addEventListener('click', function () {
                qty.value = (parseInt(qty.value, 10) || 1) - 1;
                clampQty();
            });

            plus.addEventListener('click', function () {
                qty.value = (parseInt(qty.value, 10) || 1) + 1;
                clampQty();
            });

            qty.addEventListener('input', clampQty);
            qty.addEventListener('change', clampQty);
        }

        form.addEventListener('submit', function (event) {
            if (hasSizes && !chosenSize()) {
                event.preventDefault();

                if (hint) {
                    hint.textContent = 'Please choose a size before adding to cart.';
                    hint.classList.add('is-error');
                }

                return;
            }

            clampQty();
        });

        clampQty();
    }
});
</script>

@endsection