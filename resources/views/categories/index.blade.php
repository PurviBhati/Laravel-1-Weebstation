@extends('layouts.storefront')

@section('title', 'Shop by category | WEEB STATION')

@section('content')

@php
    $audiences = [
        'men'      => 'Men',
        'women'    => 'Women',
        'everyone' => 'Everyone',
    ];

    $audienceLabel = $audiences[$audience] ?? ucfirst($audience);
@endphp

<style>
    .ws-cats {
        --k-peach: #b89191;
        --k-pink: #db6777;
        --k-wine: #310105;
        --k-rum: #380f13;
        --k-toast: #200b05;
        --k-muted: rgb(35, 56, 54);
        --k-border: rgba(255, 217, 191, 0.16);

        width: min(1200px, calc(100% - 32px));
        margin: 0 auto;
        padding: 34px 0 60px;
        color: var(--k-peach);
    }

    .ws-cats *,
    .ws-cats *::before,
    .ws-cats *::after {
        box-sizing: border-box;
    }

    .ws-cats .cats-intro {
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--k-border);
    }

    .ws-cats .cats-eyebrow {
        display: inline-block;
        margin-bottom: 12px;
        color: var(--k-pink);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2.5px;
        text-transform: uppercase;
    }

    .ws-cats h1 {
        margin: 0;
        font-size: clamp(2rem, 5vw, 3.6rem);
        font-weight: 800;
        letter-spacing: -0.045em;
        line-height: 1.08;
    }

    .ws-cats .cats-desc {
        max-width: 540px;
        margin: 12px 0 0;
        color: var(--k-muted);
        line-height: 1.8;
    }

    /* Tabs */

    .ws-cats .cats-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 28px;
    }

    .ws-cats .cats-tab {
        padding: 10px 18px;
        border: 1px solid var(--k-border);
        border-radius: 999px;
        color: var(--k-peach);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-decoration: none;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .ws-cats .cats-tab:hover {
        border-color: var(--k-pink);
    }

    .ws-cats .cats-tab.is-active {
        border-color: var(--k-peach);
        background: var(--k-peach);
        color: var(--k-toast);
    }

    /* Tiles */

    .ws-cats .cats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }

    .ws-cats .cat-tile {
        position: relative;
        display: block;
        aspect-ratio: 4 / 5;
        overflow: hidden;
        border: 1px solid var(--k-border);
        border-radius: 14px;
        background: linear-gradient(145deg, var(--k-wine), var(--k-toast));
        color: var(--k-peach);
        text-decoration: none;
        transition: border-color 0.3s ease, transform 0.3s ease;
    }

    .ws-cats .cat-tile:hover {
        border-color: rgba(255, 217, 191, 0.55);
        transform: translateY(-4px);
    }

    .ws-cats .cat-tile img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .ws-cats .cat-tile:hover img {
        transform: scale(1.05);
    }

    .ws-cats .cat-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 217, 191, 0.25);
        font-size: clamp(3rem, 8vw, 5rem);
        font-weight: 900;
        letter-spacing: -6px;
    }

    .ws-cats .cat-shade {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(32, 11, 5, 0.92) 0%, rgba(32, 11, 5, 0.1) 60%);
    }

    .ws-cats .cat-info {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        padding: 18px;
    }

    .ws-cats .cat-name {
        margin: 0 0 4px;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .ws-cats .cat-count {
        color: var(--k-muted);
        font-size: 0.8rem;
    }

    .ws-cats .cat-arrow {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        border: 1px solid rgba(255, 217, 191, 0.4);
        border-radius: 50%;
        font-size: 1.1rem;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ws-cats .cat-tile:hover .cat-arrow {
        background: var(--k-peach);
        color: var(--k-toast);
    }

    /* Bottom link and empty state */

    .ws-cats .cats-all {
        display: inline-block;
        margin-top: 28px;
        color: var(--k-pink);
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
    }

    .ws-cats .cats-all:hover {
        text-decoration: underline;
    }

    .ws-cats .cats-empty {
        padding: 60px 20px;
        border: 1px solid var(--k-border);
        border-radius: 14px;
        background: rgba(255, 217, 191, 0.04);
        text-align: center;
    }

    .ws-cats .cats-empty h2 {
        margin: 0 0 10px;
        font-size: 1.6rem;
    }

    .ws-cats .cats-empty p {
        max-width: 420px;
        margin: 0 auto;
        color: var(--k-muted);
        line-height: 1.7;
    }

    @media (max-width: 1000px) {
        .ws-cats .cats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .ws-cats .cats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .ws-cats .cat-info {
            padding: 12px;
        }

        .ws-cats .cat-name {
            font-size: 1rem;
        }

        .ws-cats .cat-arrow {
            display: none;
        }
    }
</style>

<div class="ws-cats">

    <header class="cats-intro">
        <span class="cats-eyebrow">WEEB STATION / CATEGORIES</span>

        <h1>Categories</h1>

        <p class="cats-desc" style=" color: rgba(122, 100, 85, 0.73);">
            Pick a category to see everything in it, or browse all of
            {{ $audienceLabel === 'Everyone' ? 'our unisex' : "the " . strtolower($audienceLabel) . "'s" }} collection.
        </p>
    </header>

    {{-- Men / Women / Everyone --}}
    <nav class="cats-tabs" aria-label="Category audience">
        @foreach ($audiences as $slug => $label)
            <a
                href="{{ url('/shop/categories/'. $slug) }}"
                class="cats-tab {{ $audience === $slug ? 'is-active' : '' }}"
                @if ($audience === $slug) aria-current="page" @endif
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($categories->isEmpty())

        <div class="cats-empty">
            <h2>No categories here yet.</h2>
            <p>
                We're still stocking this section. Check another audience or
                browse everything we have.
            </p>

            <a href="{{ url('/shop/everyone') }}" class="cats-all">Browse the collection &rarr;</a>
        </div>

    @else

        <div class="cats-grid">
            @foreach ($categories as $category)

                @php $cover = $covers[$category->id] ?? null; @endphp

                <a
                    href="{{ url('/shop/' . $audience . '?category=' . $category->id) }}"
                    class="cat-tile"
                    aria-label="Shop {{ $category->name }}"
                >
                    @if ($cover)
                        <img src="{{ asset('storage/' . $cover) }}" alt="" loading="lazy">
                    @else
                        <span class="cat-placeholder" aria-hidden="true">WS.</span>
                    @endif

                    <span class="cat-shade"></span>

                    <span class="cat-info">
                        <span>
                            <h2 class="cat-name">{{ $category->name }}</h2>
                            <span class="cat-count">
                                {{ $category->active_products_count }}
                                {{ $category->active_products_count === 1 ? 'item' : 'items' }}
                            </span>
                        </span>

                        <span class="cat-arrow" aria-hidden="true">↗</span>
                    </span>
                </a>

            @endforeach
        </div>

        <a href="{{ url('/shop/' . $audience) }}" class="cats-all" style="color: #94735494;">
            See all {{ strtolower($audienceLabel) }} products &rarr;
        </a>

    @endif

</div>

@endsection