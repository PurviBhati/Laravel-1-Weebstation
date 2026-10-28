@extends('layouts.storefront')
@section('title', ($title ?? 'Shop') . ' | WEEB STATION')

@section('content')

@php
    // Sorting happens here so the controller does not need to change.
    $selected = request('category', $selected ?? null);

    if (!empty($selected) && $products instanceof \Illuminate\Support\Collection){
        $products = $products->where('category_id',(int) $selected)->values();
    }

    $selectedCategory = empty($selected) ? collect($categories)->firstWhere('id',(int) $selected) : null;
    $sort = request('sort');

    if ($products instanceof \Illuminate\Support\Collection) {
        $products = match ($sort) {
            'price_asc'  => $products->sortBy('price')->values(),
            'price_desc' => $products->sortByDesc('price')->values(),
            'name'       => $products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'newest'     => $products->sortByDesc('created_at')->values(),
            default      => $products,
        };
    }

    $audiences = [
        'men'      => 'Men',
        'women'    => 'Women',
        'everyone' => 'Everyone',
    ];
@endphp

<style>
    .ws-shop {
        --ws-peach: #ffd9bf;
        --ws-pink: #cf5f81;
        --ws-wine: #752027;
        --ws-rum: #4a0a0f;
        --ws-toast: #200b05;
        --ws-muted: rgba(255, 217, 191, 0.62);
        --ws-border: rgba(255, 217, 191, 0.16);

        color: var(--ws-peach);
        padding: 24px 0 65px;
    }

    .ws-shop,
    .ws-shop * {
        box-sizing: border-box;
    }

    .ws-shop__container {
        width: min(1200px, 92%);
        margin: 0 auto;
    }

    /* Heading */

    .ws-shop__intro {
        padding: 38px 0 26px;
        margin-bottom: 26px;
        border-bottom: 1px solid var(--ws-border);
    }

    .ws-shop__eyebrow {
        display: inline-block;
        margin-bottom: 12px;
        color: var(--ws-pink);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2.5px;
        text-transform: uppercase;
    }

    .ws-shop__title {
        margin: 0;
        color: var(--ws-peach);
        font-size: clamp(34px, 5vw, 58px);
        font-weight: 800;
        letter-spacing: -2px;
        line-height: 1.08;
    }

    .ws-shop__description {
        max-width: 560px;
        margin: 14px 0 0;
        color: var(--ws-muted);
        font-size: 15px;
        line-height: 1.8;
    }

    /* Audience tabs */

    .ws-shop__tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 26px;
    }

    .ws-shop__tab {
        padding: 10px 18px;
        border: 1px solid var(--ws-border);
        border-radius: 999px;
        color: var(--ws-peach);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-decoration: none;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .ws-shop__tab:hover {
        border-color: var(--ws-pink);
    }

    .ws-shop__tab.is-active {
        border-color: var(--ws-peach);
        background: var(--ws-peach);
        color: var(--ws-toast);
    }

    /* Toolbar */

    .ws-shop__toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .ws-shop__count {
        margin: 0;
        color: var(--ws-muted);
        font-size: 13px;
    }

    .ws-shop__count strong {
        color: var(--ws-peach);
    }

    .ws-shop__filters {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 0;
    }

    .ws-shop__filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ws-shop__filter-label {
        color: var(--ws-muted);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
    }

    .ws-shop__select {
        min-width: 160px;
        max-width: 100%;
        padding: 11px 13px;
        border: 1px solid var(--ws-border);
        border-radius: 8px;
        background: rgba(32, 11, 5, 0.7);
        color: var(--ws-peach);
        font: inherit;
        font-size: 13px;
        cursor: pointer;
    }

    .ws-shop__select option {
        background: var(--ws-toast);
        color: var(--ws-peach);
    }

    .ws-shop__select:focus-visible,
    .ws-shop__button:focus-visible,
    .ws-shop__tab:focus-visible,
    .ws-product-card:focus-visible {
        outline: 2px solid var(--ws-pink);
        outline-offset: 4px;
    }

    .ws-shop__reset {
        color: var(--ws-pink);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .ws-shop__reset:hover {
        text-decoration: underline;
    }

    /* Product grid */

    .ws-product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 28px 20px;
    }

    .ws-product-card {
        display: flex;
        min-width: 0;
        flex-direction: column;
        color: inherit;
        text-decoration: none;
        border-radius: 10px;
    }

    .ws-product-card__image {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        aspect-ratio: 4 / 5;
        overflow: hidden;
        border: 1px solid var(--ws-border);
        border-radius: 10px;
        background: linear-gradient(145deg, var(--ws-wine), var(--ws-toast));
        transition: border-color 0.3s ease;
    }

    .ws-product-card:hover .ws-product-card__image {
        border-color: rgba(255, 217, 191, 0.5);
    }

    .ws-product-card__image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .ws-product-card:hover .ws-product-card__image img {
        transform: scale(1.045);
    }

    .ws-product-card__placeholder {
        display: flex;
        width: 100%;
        height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--ws-peach);
    }

    .ws-product-card__placeholder[hidden] {
        display: none;
    }

    .ws-product-card__placeholder strong {
        font-size: clamp(36px, 5vw, 58px);
        font-weight: 900;
        letter-spacing: -4px;
    }

    .ws-product-card__placeholder span {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 3px;
    }

    .ws-product-card__tag {
        position: absolute;
        z-index: 2;
        top: 12px;
        left: 12px;
        padding: 7px 10px;
        border-radius: 4px;
        background: var(--ws-peach);
        color: var(--ws-rum);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .ws-product-card__tag--sold {
        background: #000;
        color: #fff;
    }

    .ws-product-card.is-sold-out .ws-product-card__image img {
        opacity: 0.45;
    }

    .ws-product-card__info {
        display: flex;
        flex: 1;
        flex-direction: column;
        padding: 15px 2px 0;
    }

    .ws-product-card__category {
        margin: 0 0 7px;
        color: var(--ws-muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .ws-product-card__name {
        margin: 0;
        color: var(--ws-peach);
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .ws-product-card__bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 12px;
    }

    .ws-product-card__price {
        color: var(--ws-peach);
        font-size: 16px;
        font-weight: 800;
    }

    .ws-product-card__stock {
        color: var(--ws-muted);
        font-size: 11px;
        text-align: right;
    }

    .ws-product-card__stock.is-low {
        color: var(--ws-pink);
        font-weight: 700;
    }

    /* Empty state */

    .ws-shop__empty {
        grid-column: 1 / -1;
        padding: 65px 20px;
        border: 1px solid var(--ws-border);
        border-radius: 12px;
        background: rgba(255, 217, 191, 0.04);
        text-align: center;
    }

    .ws-shop__empty-symbol {
        margin-bottom: 15px;
        color: var(--ws-pink);
        font-size: 42px;
    }

    .ws-shop__empty h2 {
        margin: 0;
        color: var(--ws-peach);
        font-size: 25px;
    }

    .ws-shop__empty p {
        max-width: 440px;
        margin: 12px auto 22px;
        color: var(--ws-muted);
        font-size: 14px;
        line-height: 1.8;
    }

    .ws-shop__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 12px 20px;
        border: 1px solid var(--ws-peach);
        border-radius: 5px;
        background: var(--ws-peach);
        color: var(--ws-toast);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.7px;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ws-shop__button:hover {
        background: transparent;
        color: var(--ws-peach);
    }

    /* Footer */

    .ws-shop__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 50px;
        padding-top: 22px;
        border-top: 1px solid var(--ws-border);
        color: var(--ws-muted);
        font-size: 12px;
    }

    .ws-shop__footer a {
        color: var(--ws-pink);
        font-weight: 700;
        text-decoration: none;
    }

    /* Responsive */

    @media (max-width: 1000px) {
        .ws-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 25px 16px;
        }
    }

    @media (max-width: 650px) {
        .ws-shop {
            padding-top: 10px;
        }

        .ws-shop__intro {
            padding-top: 28px;
        }

        .ws-shop__title {
            letter-spacing: -1px;
        }

        .ws-shop__toolbar {
            align-items: flex-start;
            flex-direction: column;
            gap: 15px;
        }

        .ws-shop__filters {
            width: 100%;
        }

        .ws-shop__filter-group {
            flex: 1 1 100%;
        }

        .ws-shop__select {
            flex: 1;
            min-width: 0;
        }

        .ws-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px 12px;
        }

        .ws-product-card__info {
            padding-top: 12px;
        }

        .ws-product-card__name {
            font-size: 13px;
        }

        .ws-product-card__price {
            font-size: 14px;
        }

        .ws-product-card__tag {
            top: 8px;
            left: 8px;
            padding: 6px 7px;
            font-size: 9px;
        }

        .ws-shop__footer {
            margin-top: 35px;
        }
    }
</style>

<div class="ws-shop">
    <div class="ws-shop__container">

        {{-- Page heading --}}
        <header class="ws-shop__intro">
        <span class="ws-shop__eyebrow"> WEEB STATION / THE COLLECTION
            @if ($selectedCategory)
             {{ strtoupper($selectedCategory->name) }}
             @endif
        </span>
            <h1 class="ws-shop__title"> {{ $selectedCategory->name ?? ($title ?? 'Explore the collection') }}</h1>

            <p class="ws-shop__description">
                Find your next favourite. Explore the collection and discover
                pieces chosen to match your style.
            </p>
        </header>

        {{-- Audience tabs --}}
        <nav class="ws-shop__tabs" aria-label="Shop by audience">
            @foreach ($audiences as $slug => $label)
                <a
                    style="color: #c2535c;"
                    href="{{ url('/shop/categories/' . $slug) }}"
                    class="ws-shop__tab {{ $audience === $slug ? 'is-active' : '' }}"
                    @if ($audience === $slug) aria-current="page" @endif
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Count, category filter and sorting --}}
        <div class="ws-shop__toolbar">

            <p class="ws-shop__count" style="color: #4a0a0f;">
                Showing <strong style="color: #aa4e56;">{{ $products->count() }}</strong>
                {{ $products->count() === 1 ? 'product' : 'products' }}
            </p>

            <form method="GET" action="{{ url('/shop/' . $audience) }}"class="ws-shop__filters">
                <div class="ws-shop__filter-group">
                    <label for="ws-category-filter" class="ws-shop__filter-label">
                        CATEGORY
                    </label>

                    <select id="ws-category-filter" name="category" class="ws-shop__select" onchange="this.form.submit()">
                        <option value="">All categories</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ (string) $selected === (string) $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="ws-shop__filter-group">
                    <label for="ws-sort" class="ws-shop__filter-label">
                        SORT
                    </label>

                    <select id="ws-sort" name="sort" class="ws-shop__select" onchange="this.form.submit()">
                        <option value="">Featured</option>
                        <option value="newest"     {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc"  {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="name"       {{ $sort === 'name' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>

                @if (!empty($selected) || !empty($sort))
                    <a class="ws-shop__reset" href="{{ url('/shop/' . $audience) }}">
                        Clear filters
                    </a>
                @endif

                <noscript>
                    <button type="submit" class="ws-shop__button">Apply</button>
                </noscript>
            </form>
        </div>

        {{-- Product grid --}}
        <div class="ws-product-grid">

            @forelse ($products as $product)

                @php
                    $firstImage   = $product->images->first();
                    $categoryName = $product->category->name ?? 'WEEB STATION';
                    $isSoldOut    = $product->stock <= 0;
                    $isLow        = !$isSoldOut && $product->stock <= 5;
                @endphp

                <a
                    class="ws-product-card {{ $isSoldOut ? 'is-sold-out' : '' }}"
                    href="{{ url('/product/' . $product->id) }}"
                    aria-label="View {{ $product->name }}"
                >
                    <div class="ws-product-card__image">

                        @if ($isSoldOut)
                            <span class="ws-product-card__tag ws-product-card__tag--sold">
                                Sold out
                            </span>
                        @elseif (!empty($product->is_featured))
                            <span class="ws-product-card__tag">
                                Featured
                            </span>
                        @endif

                        @if ($firstImage && $firstImage->path)
                            <img
                                src="{{ asset('storage/' . $firstImage->path) }}"
                                alt="{{ $product->name }}"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.hidden=false;"
                            >

                            <div class="ws-product-card__placeholder" hidden>
                                <strong>WS.</strong>
                                <span>WEEB STATION</span>
                            </div>
                        @else
                            <div class="ws-product-card__placeholder">
                                <strong>WS.</strong>
                                <span>WEEB STATION</span>
                            </div>
                        @endif

                    </div>

                    <div class="ws-product-card__info">

                        <p class="ws-product-card__category">
                            {{ $categoryName }}
                        </p>

                        <h2 class="ws-product-card__name">
                            {{ $product->name }}
                        </h2>

                        <div class="ws-product-card__bottom">
                            <span class="ws-product-card__price">
                                ₹{{ number_format((float) $product->price, 2) }}
                            </span>

                            <span class="ws-product-card__stock {{ $isLow ? 'is-low' : '' }}">
                                @if ($isSoldOut)
                                    Out of stock
                                @elseif ($isLow)
                                    Only {{ $product->stock }} left
                                @else
                                    In stock
                                @endif
                            </span>
                        </div>

                    </div>
                </a>

            @empty

                <div class="ws-shop__empty">
                    <div class="ws-shop__empty-symbol" aria-hidden="true">✳</div>

                    <h2>Nothing in this section. Yet.</h2>

                    <p>
                        This collection is waiting for its next favourite.
                        Try another category or browse all available styles.
                    </p>

                    <a href="{{ url('/shop/' . $audience) }}" class="ws-shop__button">
                        BROWSE ALL STYLES
                    </a>
                </div>

            @endforelse

        </div>

        {{-- Bottom navigation --}}
        <footer class="ws-shop__footer">
            <span>WEEB STATION — Find your style.</span>

            <a href="{{ url('/shop/everyone') }}">Explore everything &rarr;</a>
        </footer>

    </div>
</div>

@endsection