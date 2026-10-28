<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'WEEB STATION') | WEEB STATION</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f5f7fb;
            --surface: #ffffff;
            --sidebar: #151c2c;
            --sidebar-hover: #263249;
            --text: #182230;
            --muted: #718096;
            --border: #e4e9f1;
            --accent: #315efb;
            --accent-hover: #2448d8;
            --danger: #dc3545;
            --success-bg: #d9f8e9;
            --success-text: #087443;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Bricolage Grotesque", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button, input, select, textarea {
            font: inherit;
        }

        .admin-shell {
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 245px;
            padding: 26px 16px;
            background: var(--sidebar);
            color: #e8edf6;
            display: flex;
            flex-direction: column;
            z-index: 20;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 10px 28px;
            border-bottom: 1px solid rgba(255,255,255,.1);
            font-size: 17px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #fff;
            color: var(--sidebar);
            font-size: 22px;
            font-weight: 700;
        }

        .nav-label {
            margin: 27px 10px 12px;
            color: #8997ad;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 45px;
            padding: 11px 13px;
            border-radius: 9px;
            color: #c9d2e1;
            font-size: 14px;
            font-weight: 500;
            transition: background .18s, color .18s;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .nav-link.active {
            background: #2b3b56;
            color: #fff;
        }

        .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 18px 10px 0;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        .user-name {
            overflow-wrap: anywhere;
            margin-bottom: 12px;
            color: #e8edf6;
            font-size: 13px;
        }

        .logout-button {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 8px;
            background: transparent;
            color: #e8edf6;
            cursor: pointer;
            text-align: left;
        }

        .logout-button:hover {
            background: var(--sidebar-hover);
        }

        /* Main content */
        .main-area {
            min-height: 100vh;
            margin-left: 245px;
        }

        .topbar {
            min-height: 68px;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .topbar-title {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .topbar-user {
            color: var(--muted);
            font-size: 13px;
        }

        main {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 32px;
        }

        h1, h2, h3 {
            margin-top: 0;
            color: var(--text);
        }

        h1 {
            margin-bottom: 0;
            font-size: 25px;
            font-weight: 700;
        }

        h2 {
            font-size: 19px;
        }

        .page-subtitle {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        /* Cards and page headers */
        .card {
            padding: 26px;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: var(--surface);
            box-shadow: 0 3px 12px rgba(20, 35, 60, .035);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            transition: background .18s;
        }

        .btn:hover {
            background: var(--accent-hover);
            color: #fff;
        }

        .btn-light {
            background: #fff;
            color: var(--text);
            border-color: var(--border);
        }

        .btn-light:hover {
            background: #f1f4f9;
            color: var(--text);
        }

        .btn-danger {
            background: #fff;
            border-color: #f5c9ce;
            color: var(--danger);
        }

        .btn-danger:hover {
            background: #fff0f1;
            color: #b42332;
        }

        /* Forms */
        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
        }

        input:not([type=checkbox]):not([type=radio]):not([type=file]),
        select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d6deea;
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            font-size: 14px;
        }

        input:focus, select:focus, textarea:focus {
            outline: 2px solid rgba(49,94,251,.17);
            border-color: var(--accent);
        }

        input::placeholder, textarea::placeholder {
            color: #9aa5b5;
        }

        input[type=file] {
            max-width: 100%;
            color: var(--muted);
        }

        .error {
            margin-top: 5px;
            color: #c62838;
            font-size: 13px;
        }

        .alert {
            padding: 13px 16px;
            margin-bottom: 22px;
            border: 1px solid #b9efd2;
            border-radius: 9px;
            background: var(--success-bg);
            color: var(--success-text);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .check label {
            margin: 0;
        }

        .status-options, .radio-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            min-height: 44px;
        }

        .status-options label, .radio-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            font-weight: 400;
        }

        .side-layout {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 16px;
            align-items: start;
        }

        /* Tables */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 13px 12px;
            background: #f7f9fc;
            color: #68778d;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        th:first-child {
            border-radius: 7px 0 0 7px;
        }

        th:last-child {
            border-radius: 0 7px 7px 0;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #edf0f5;
            color: #364254;
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #fafbfe;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .thumb {
            display: block;
            width: 52px;
            height: 58px;
            object-fit: cover;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: #f3f5f8;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-draft {
            background: #eef0f4;
            color: #536174;
        }

        .badge-active {
            background: #d9f8e9;
            color: #087443;
        }

        .badge-archived {
            background: #fff0c7;
            color: #8b5a00;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 205px;
            }

            .main-area {
                margin-left: 205px;
            }

            main {
                padding: 22px 18px;
            }

            .card {
                padding: 20px;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: static;
                width: 100%;
                padding: 14px;
            }

            .brand {
                padding: 0 8px 14px;
            }

            .nav-label {
                margin-top: 16px;
            }

            .sidebar nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .nav-link {
                padding: 10px;
            }

            .sidebar-bottom {
                margin-top: 16px;
                padding-top: 12px;
            }

            .main-area {
                margin-left: 0;
            }

            .topbar {
                min-height: 52px;
                padding: 12px 18px;
            }

            main {
                padding: 18px 12px;
            }

            .header {
                align-items: flex-start;
            }

            .grid-2, .side-layout {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 16px;
                border-radius: 10px;
            }
        }
    </style>
</head>

<body>
    @if (request()->is('admin*'))
        <div class="admin-shell">
            <aside class="sidebar">
                <a href="/admin/products" class="brand">
                    <span class="brand-mark">W</span>
                    <span>WEEB STATION</span>
                </a>

                <div class="nav-label">Store Management</div>

                <nav aria-label="Admin navigation">
                    <a href="/admin/products"
                       class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span>Store</span>
                    </a>

                    <a href="/admin/products/create"
                       class="nav-link {{ request()->is('admin/products/create') ? 'active' : '' }}">
                        <span class="nav-icon">✅</span>
                        <span>Add Product</span>
                    </a>
                    <a href="/admin/sizes"
                       class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"">
                        <span class="nav-icon">📝</span>
                        <span>Size table</span>
                    </a>
                    <a href="/admin/sales"
                       class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"">
                        <span class="nav-icon">💰</span>
                        <span>Sales</span>
                    </a>
                
                    <a href="/admin/orders/"
                        class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                            <span class="nav-icon"></span>
                            <span>Orders</span>
                    </a>
                </nav>

                <div class="sidebar-bottom">
                    @auth
                        <div class="user-name">
                            Signed in as <strong>{{ auth()->user()->name }}</strong>
                        </div>

                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="logout-button">↪ &nbsp; Logout</button>
                        </form>
                    @endauth
                </div>
            </aside>

            <div class="main-area">
                <header class="topbar">
                    <p class="topbar-title">@yield('page-heading', 'Store Management')</p>
                    @auth
                        <span class="topbar-user">{{ auth()->user()->name }}</span>
                    @endauth
                </header>

                <main>
                    @if (session('success'))
                        <div class="alert">{{ session('success') }}</div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <header class="topbar">
            <a href="/" class="topbar-title">WEEB STATION</a>
            <div style="display:flex; align-items:center; gap:18px;">
                @auth
                    <a href="/admin/products">Admin panel</a>
                    <span class="topbar-user">Hi, {{ auth()->user()->name }}</span>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="btn btn-light">Logout</button>
                    </form>
                @else
                    <a href="/login">Login</a>
                @endauth
            </div>
        </header>

        <main>
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @yield('content')
        </main>
    @endif
</body>
</html>
