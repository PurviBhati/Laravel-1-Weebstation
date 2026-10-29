<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'WEEB STATION | Wear Your Energy')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&display=swap" rel="stylesheet">

    <style>
        :root {
            --ws-peach: #ffd9bf;
            --ws-pink: #d86e7c;
            --ws-wine: #752027;
            --ws-rum: #4a0a0f;
            --ws-toast: #200b05;
            --ws-muted: rgba(255, 217, 191, 0.62);
            --ws-border: rgba(255, 217, 191, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Bricolage Grotesque", system-ui, sans-serif;
            color: var(--ws-peach);
            background:
                radial-gradient(ellipse at 30% 80%, rgba(117, 32, 39, 0.35), transparent 40%),
                var(--ws-toast);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        h1, h2, h3, h4 {
            color: inherit;
        }

        main {
            flex: 1;
            width: 100%;
        }

        /* ---------- Header ---------- */

        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid var(--ws-border);
            background: rgba(85, 36, 36, 0.99);
            backdrop-filter: blur(10px);
        }

        .site-header__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px 20px;
            width: min(1240px, calc(100% - 32px));
            min-height: 68px;
            margin: 0 auto;
        }

        .site-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .site-brand__mark {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--ws-peach);
            color: var(--ws-toast);
            font-weight: 900;
        }

        .site-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .site-nav a {
            padding: 9px 14px;
            border-radius: 999px;
            color: var(--ws-muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .site-nav a:hover,
        .site-nav a.is-active {
            background: rgba(255, 217, 191, 0.1);
            color: var(--ws-peach);
        }

        .site-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            white-space: nowrap;
        }

        .site-actions form {
            display: flex;
            align-items: center;
            margin: 0;
        }

        .site-actions a:hover {
            color: var(--ws-pink);
        }

        .site-link-button {
            padding: 0;
            border: 0;
            background: none;
            color: inherit;
            cursor: pointer;
        }

        .site-link-button:hover {
            color: var(--ws-pink);
        }

        .cart-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid var(--ws-border);
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            transition: border-color 0.2s ease;
        }

        .cart-link:hover {
            border-color: var(--ws-peach);
            color: var(--ws-peach);
        }

        .cart-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--ws-pink);
            color: var(--ws-toast);
            font-size: 11px;
            font-weight: 800;
        }

        /* ---------- Flash messages ---------- */

        .flash-wrap {
            width: min(1240px, calc(100% - 32px));
            margin: 18px auto 0;
        }

        .flash {
            padding: 13px 16px;
            margin-bottom: 10px;
            border: 1px solid rgba(120, 220, 160, 0.4);
            border-radius: 10px;
            background: rgba(60, 170, 110, 0.16);
            color: #b8f2d0;
            font-size: 14px;
        }

        .flash--error {
            border-color: rgba(255, 120, 120, 0.4);
            background: rgba(255, 90, 90, 0.14);
            color: #ffc4c4;
        }

        /* ---------- Footer ---------- */

        .site-footer {
            margin-top: 40px;
            border-top: 1px solid var(--ws-border);
        }

        .site-footer__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            width: min(1240px, calc(100% - 32px));
            margin: 0 auto;
            padding: 26px 0;
            color: var(--ws-muted);
            font-size: 13px;
        }

        .site-footer__links {
            display: flex;
            gap: 18px;
        }

        .site-footer__links a:hover {
            color: var(--ws-peach);
        }

        @media (max-width: 760px) {
            .site-nav {
                order: 3;
                width: 100%;
                justify-content: center;
                padding-bottom: 10px;
            }

            .site-header__inner {
                min-height: 60px;
            }
        }

        @media (max-width: 480px) {
            .site-actions .site-hide-small {
                display: none;
            }
        }
    </style>
</head>

<body>

    @php
        // Number of items in the cart (the cart is kept in the session).
        $cartCount = \App\Support\CartStore::count();

        // Number of products in the wishlist (also kept in the session).
        $wishCount = \App\Support\WishlistStore::count();
    @endphp

    <header class="site-header">
        <div class="site-header__inner">

            <a href="{{ url('/') }}" class="site-brand">
                <span class="site-brand__mark">W</span>
                <span>WEEB STATION</span>
            </a>

            <nav class="site-nav" aria-label="Shop">
                <a href="{{ url('/categories') }}"
                   class="{{ request()->is('categories') || request()->is('shop/*/categories') ? 'is-active' : '' }}">Categories</a>
            </nav>

            <div class="site-actions">
                @auth('customer')
                    <a href="{{ route('account.index') }}" class="site-hide-small">
                        Hi, {{ \Illuminate\Support\Str::words(auth('customer')->user()->name, 1, '') }}
                    </a>
                    <form method="POST" action="{{ route('account.logout') }}">
                        @csrf
                        <button type="submit" class="site-link-button site-hide-small">Logout</button>
                    </form>
                @else
                    <a href="{{ route('account.login') }}" class="site-hide-small">Login</a>
                @endauth

                <a href="{{ url('user/wishlist') }}" class="cart-link" aria-label="Wishlist, {{ $wishCount }} items">
                    <span aria-hidden="true">♡</span>
                    <span class="site-hide-small">Wishlist</span>
                    @if ($wishCount > 0)
                        <span class="cart-count">{{ $wishCount }}</span>
                    @endif
                </a>

                <a href="{{ url('user/cart') }}" class="cart-link" aria-label="Cart, {{ $cartCount }} items">
                    <span aria-hidden="true">🛍</span>
                    <span>Cart</span>
                    <span class="cart-count">{{ $cartCount }}</span>
                </a>
            </div>

        </div>
    </header>

    @if (session('success') || session('error'))
        <div class="flash-wrap">
            @if (session('success'))
                <div class="flash" data-auto-hide>{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="flash flash--error" data-auto-hide="6000">{{ session('error') }}</div>
            @endif
        </div>
    @endif

    <main>
        @yield('account')
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer__inner">
            <span>© {{ date('Y') }} WEEB STATION. Wear your energy.</span>
        </div>
    </footer>

    <script>
    // Messages marked with data-auto-hide fade out by themselves.
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-auto-hide]').forEach(function (box) {
            const delay = Number(box.getAttribute('data-auto-hide')) || 3500;

            setTimeout(function () {
                box.style.transition = 'opacity 0.4s ease';
                box.style.opacity = '0';

                setTimeout(function () { box.remove(); }, 400);
            }, delay);
        });
    });
    </script>

</body>
</html>