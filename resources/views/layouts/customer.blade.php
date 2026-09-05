<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Veripay')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('rage-favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('rage-assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('rage-assets/css/animate.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700|Raleway:400,600,700|Open+Sans:300,400,600,700');

        :root {
            --shop-bg: #f7f7f7;
            --shop-surface: #ffffff;
            --shop-ink: #1f2933;
            --shop-muted: #68717d;
            --shop-line: #e8e8e8;
            --shop-primary: #ff6863;
            --shop-primary-strong: #ef4f4a;
            --shop-green: #16835b;
            --shop-accent: #ffbf00;
            --shop-soft: #fff1f0;
            --shop-shadow: 0 14px 34px rgba(32, 35, 41, 0.08);
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--shop-bg);
            color: var(--shop-ink);
            font-family: 'Open Sans', Arial, sans-serif;
        }

        .store-shell {
            width: min(100%, 1200px);
            margin: 0 auto;
            padding: 0 16px;
        }

        .store-header {
            position: sticky;
            top: 0;
            z-index: 80;
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid var(--shop-line);
            backdrop-filter: blur(12px);
        }

        .store-header-inner {
            min-height: 72px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 18px;
            padding: 12px 0;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--shop-ink);
            font-family: 'Montserrat', Arial, sans-serif;
            font-weight: 700;
            letter-spacing: 0;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            display: inline-grid;
            place-items: center;
            border-radius: 6px;
            background: var(--shop-primary);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(255, 104, 99, 0.28);
        }

        .header-search {
            position: relative;
            max-width: 640px;
            width: 100%;
            margin: 0 auto;
        }

        .header-search input,
        .shop-input,
        .shop-select {
            width: 100%;
            border: 1px solid var(--shop-line);
            background: #ffffff;
            color: var(--shop-ink);
            border-radius: 6px;
            padding: 13px 14px;
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .header-search input {
            padding-left: 46px;
            padding-right: 110px;
            border-radius: 6px;
            background: #f8fafc;
        }

        .header-search input:focus,
        .shop-input:focus,
        .shop-select:focus {
            border-color: rgba(255, 104, 99, 0.55);
            box-shadow: 0 0 0 4px rgba(255, 104, 99, 0.13);
        }

        .search-icon {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--shop-muted);
        }

        .search-submit {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            border-radius: 4px;
            padding: 10px 18px;
            background: var(--shop-primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
        }

        .header-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            white-space: nowrap;
        }

        .icon-button {
            position: relative;
            width: 44px;
            height: 44px;
            display: inline-grid;
            place-items: center;
            border: 1px solid var(--shop-line);
            border-radius: 6px;
            background: #ffffff;
            color: var(--shop-ink);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease;
        }

        .icon-button-form {
            margin: 0;
            display: inline-flex;
        }

        .icon-button-form .icon-button {
            cursor: pointer;
        }

        .icon-button:active,
        .shop-btn:active,
        .shop-card:active {
            transform: scale(0.97);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 20px;
            height: 20px;
            display: inline-grid;
            place-items: center;
            border-radius: 999px;
            background: var(--shop-primary);
            color: #ffffff;
            border: 2px solid #ffffff;
            font-size: 11px;
            font-weight: 800;
        }

        .page-main {
            padding-top: 22px;
            padding-bottom: 108px;
        }

        .rage-hero {
            position: relative;
            overflow: hidden;
            min-height: 430px;
            display: grid;
            align-items: center;
            background:
                linear-gradient(90deg, rgba(23, 25, 31, 0.78), rgba(23, 25, 31, 0.36)),
                url('{{ asset('rage-assets/images/background/header.jpg') }}') center/cover;
            color: #ffffff;
        }

        .rage-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            background: var(--shop-primary);
        }

        .rage-hero-content {
            position: relative;
            z-index: 1;
            width: min(100%, 680px);
            padding: 44px;
        }

        .rage-hero h1 {
            margin: 0;
            font-family: 'Raleway', Arial, sans-serif;
            font-size: clamp(38px, 7vw, 70px);
            line-height: 1.02;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
        }

        .rage-hero p {
            margin-top: 18px;
            max-width: 560px;
            color: rgba(255, 255, 255, 0.86);
            font-size: 16px;
            line-height: 1.8;
            font-weight: 600;
        }

        .rage-hero-actions {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .shop-card {
            background: var(--shop-surface);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 6px;
            box-shadow: var(--shop-shadow);
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 4px;
            padding: 13px 18px;
            font-weight: 800;
            transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease;
        }

        .shop-btn-primary {
            background: var(--shop-primary);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(7, 89, 133, 0.22);
        }

        .shop-btn-primary:hover {
            background: var(--shop-primary-strong);
        }

        .shop-btn-secondary {
            background: #eef6ff;
            color: var(--shop-primary);
        }

        .shop-btn-outline {
            background: #ffffff;
            color: var(--shop-ink);
            border: 1px solid var(--shop-line);
        }

        .product-card {
            overflow: hidden;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 6px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
            transition: transform 170ms ease, box-shadow 170ms ease;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.11);
        }

        .product-media {
            position: relative;
            min-height: 104px;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: #ffffff;
        }

        .product-media img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain !important;
            object-position: center;
            padding: 10px;
            background: #ffffff;
        }

        .product-card .product-media {
            aspect-ratio: 1.18 / 1;
            min-height: 0;
            border-bottom: 1px solid var(--shop-line);
        }

        .product-card > div:last-child {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-media .product-glyph {
            position: relative;
            width: 58px;
            height: 70px;
            display: grid;
            place-items: center;
            border-radius: 6px;
            background: #ffffff;
            color: var(--shop-primary);
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.08em;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.16);
        }

        .media-produce,
        .media-beverages,
        .media-snacks,
        .media-household,
        .media-frozen,
        .media-default {
            background: #ffffff;
        }

        .rating-stars {
            color: #f59e0b;
            letter-spacing: -0.08em;
            font-size: 12px;
        }

        .section-title {
            font-family: 'Raleway', Arial, sans-serif;
            font-size: clamp(22px, 4vw, 32px);
            line-height: 1.1;
            letter-spacing: 0;
            font-weight: 700;
            color: var(--shop-ink);
        }

        .section-kicker {
            color: var(--shop-primary);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .shop-grid {
            display: grid;
            gap: 24px;
        }

        .shop-filter-panel,
        .catalog-heading {
            background: #ffffff;
            border: 1px solid var(--shop-line);
            border-radius: 6px;
            box-shadow: var(--shop-shadow);
        }

        .shop-filter-panel {
            height: fit-content;
            padding: 18px;
        }

        .filter-title {
            margin-top: 4px;
            font-family: 'Raleway', Arial, sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--shop-ink);
        }

        .field-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--shop-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .stock-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--shop-line);
            background: #fbfbfb;
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 700;
            color: var(--shop-ink);
        }

        .stock-toggle input {
            width: 18px;
            height: 18px;
            accent-color: var(--shop-primary);
        }

        .self-checkout-panel {
            margin-top: 22px;
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            align-items: start;
            border-top: 3px solid var(--shop-primary);
            background: #fafafa;
            padding: 16px;
        }

        .self-checkout-panel i {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            background: var(--shop-primary);
            color: #ffffff;
            border-radius: 4px;
            font-size: 18px;
        }

        .self-checkout-panel p {
            margin-top: 4px;
            color: var(--shop-muted);
            font-size: 13px;
            line-height: 1.6;
            font-weight: 600;
        }

        .catalog-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px;
        }

        .catalog-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--shop-muted);
        }

        .catalog-tags span {
            border: 1px solid var(--shop-line);
            border-radius: 4px;
            background: #ffffff;
            padding: 8px 10px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .product-grid .product-card {
            min-width: 0;
        }

        .horizontal-rail {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(150px, 190px);
            gap: 14px;
            overflow-x: auto;
            padding: 4px 2px 14px;
            scroll-snap-type: x proximity;
        }

        .horizontal-rail > * {
            scroll-snap-align: start;
        }

        .bottom-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 90;
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid var(--shop-line);
            box-shadow: 0 -16px 40px rgba(15, 23, 42, 0.09);
            padding: 8px 10px calc(8px + env(safe-area-inset-bottom));
        }

        .bottom-nav-grid {
            width: min(100%, 520px);
            margin: 0 auto;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .bottom-nav-grid::-webkit-scrollbar {
            display: none;
        }

        .bottom-nav a {
            flex: 0 0 72px;
            min-height: 58px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border-radius: 6px;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
        }

        .bottom-nav form {
            flex: 0 0 72px;
            margin: 0;
        }

        .bottom-nav button {
            width: 100%;
            min-height: 58px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #64748b;
            font: inherit;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .bottom-nav a.active,
        .bottom-nav a:hover,
        .bottom-nav button:hover {
            color: var(--shop-primary);
            background: #e0f2fe;
        }

        .fly-dot {
            position: fixed;
            z-index: 9999;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--shop-accent);
            pointer-events: none;
            box-shadow: 0 10px 24px rgba(249, 115, 22, 0.38);
            transition: transform 520ms cubic-bezier(.2,.8,.2,1), opacity 520ms ease;
        }

        .mobile-action-bar {
            bottom: calc(78px + env(safe-area-inset-bottom));
        }

        @media (max-width: 760px) {
            .store-header-inner {
                grid-template-columns: 1fr auto;
                gap: 12px;
            }

            .brand-mark span:last-child {
                font-size: 18px;
            }

            .header-search {
                grid-column: 1 / -1;
                order: 3;
                max-width: none;
            }

            .header-search input {
                padding-right: 84px;
                font-size: 14px;
            }

            .search-submit {
                padding-inline: 12px;
            }

            .page-main {
                padding-top: 16px;
            }
        }

        @media (min-width: 900px) {
            .page-main {
                padding-bottom: 48px;
            }

            .bottom-nav {
                display: none;
            }

            .mobile-action-bar {
                bottom: 0;
            }
        }

        .desktop-nav {
            display: none;
            border-top: 1px solid rgba(226, 232, 240, 0.9);
            background: #ffffff;
        }

        .desktop-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 52px;
        }

        .desktop-nav-links,
        .desktop-nav-action {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .desktop-nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 4px;
            color: #475569;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: background 140ms ease, color 140ms ease;
        }

        .desktop-nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .desktop-nav-link.active {
            background: var(--shop-soft);
            color: var(--shop-primary);
        }

        .desktop-nav-action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 4px;
            border: 1px solid var(--shop-line);
            background: #ffffff;
            color: var(--shop-ink);
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: background 140ms ease, color 140ms ease, border-color 140ms ease;
        }

        .desktop-nav-action-link:hover {
            background: var(--shop-soft);
            border-color: rgba(255, 104, 99, 0.28);
            color: var(--shop-primary);
        }

        @media (min-width: 900px) {
            .desktop-nav {
                display: block;
            }

            .shop-grid {
                grid-template-columns: 300px 1fr;
            }

            .shop-filter-panel {
                position: sticky;
                top: 140px;
            }

            .product-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }
        }

        @media (min-width: 1180px) {
        }

        @media (max-width: 640px) {
            .catalog-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .rage-hero {
                min-height: 500px;
            }

            .rage-hero-content {
                padding: 28px;
            }
        }
    </style>
    @stack('head')
</head>
@php
    $currentRoute = request()->route()?->getName();
    $accountRoute = auth()->check() ? route('home') : route('login');
@endphp
<body>
<header class="store-header">
    <div class="store-shell">
        <div class="store-header-inner">
            <a href="{{ route('home') }}" class="brand-mark" aria-label="Veripay home">
                <span class="brand-icon">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 8h13l-1.4 8.2a2 2 0 0 1-2 1.8H9.1a2 2 0 0 1-2-1.6L5.3 4H3" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 21h.01M16 21h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>Veripay</span>
            </a>

            <form class="header-search" action="{{ route('shop') }}" method="GET" role="search">
                <span class="search-icon">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.2-4.2M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </span>
                <input name="search" value="{{ request('search') }}" placeholder="Search groceries, snacks, household items..." autocomplete="off">
                <button class="search-submit" type="submit">Search</button>
            </form>

            <div class="header-actions">
                <a href="{{ route('cart') }}" class="icon-button" id="header-cart-link" aria-label="Cart">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 7h14l-1.7 8.6a2 2 0 0 1-2 1.6H9.2a2 2 0 0 1-2-1.7L5.2 3.8H3" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 21h.01M17 21h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span class="cart-badge" id="cart-count">0</span>
                </a>
                @auth
                    <form class="icon-button-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="icon-button" type="submit" aria-label="Logout">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 17l5-5-5-5M15 12H3m12-8h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ $accountRoute }}" class="icon-button" aria-label="Account">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
<nav class="desktop-nav" aria-label="Primary navigation">
    <div class="store-shell">
        <div class="desktop-nav-inner">
            <div class="desktop-nav-links">
                <a href="{{ route('home') }}" class="desktop-nav-link {{ $currentRoute === 'home' ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop') }}" class="desktop-nav-link {{ $currentRoute === 'shop' ? 'active' : '' }}">Shop</a>
                <a href="{{ route('scan') }}" class="desktop-nav-link {{ $currentRoute === 'scan' ? 'active' : '' }}">Scan</a>
                <a href="{{ route('cart') }}" class="desktop-nav-link {{ $currentRoute === 'cart' ? 'active' : '' }}">Cart</a>
                @auth
                    <a href="{{ route('history') }}" class="desktop-nav-link {{ $currentRoute === 'history' ? 'active' : '' }}">History</a>
                    <a href="{{ route('password.change') }}" class="desktop-nav-link {{ $currentRoute === 'password.change' ? 'active' : '' }}">Password</a>
                @endauth
                <a href="{{ route('checkout') }}" class="desktop-nav-link {{ $currentRoute === 'checkout' ? 'active' : '' }}">Checkout</a>
            </div>
            <div class="desktop-nav-action">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="desktop-nav-action-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="desktop-nav-action-link">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="store-shell page-main">
    @yield('content')
</main>

<nav class="bottom-nav" aria-label="Mobile navigation">
    <div class="bottom-nav-grid">
        <a href="{{ route('home') }}" class="{{ $currentRoute === 'home' ? 'active' : '' }}">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.8 12 3l9 7.8V21h-6v-6H9v6H3V10.8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Home
        </a>
        <a href="{{ route('shop') }}" class="{{ $currentRoute === 'shop' ? 'active' : '' }}">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m21 21-4.2-4.2M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Shop
        </a>
        <a href="{{ route('scan') }}" class="{{ $currentRoute === 'scan' ? 'active' : '' }}">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M7 4v3m10-3v3M6 11h12M8 15h8M10 19h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Scan
        </a>
        <a href="{{ route('cart') }}" class="{{ $currentRoute === 'cart' ? 'active' : '' }}">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 7h14l-1.7 8.6a2 2 0 0 1-2 1.6H9.2a2 2 0 0 1-2-1.7L5.2 3.8H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Cart
        </a>
        @auth
            <a href="{{ route('history') }}" class="{{ $currentRoute === 'history' ? 'active' : '' }}">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4h10M7 8h10M7 12h10M7 16h6M5 2h14a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                History
            </a>
            <a href="{{ route('password.change') }}" class="{{ $currentRoute === 'password.change' ? 'active' : '' }}">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v10H6V11Zm6 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Password
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m12-8h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Logout
                </button>
            </form>
        @else
            <a href="{{ $accountRoute }}" class="{{ in_array($currentRoute, ['login', 'register'], true) ? 'active' : '' }}">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Account
            </a>
        @endauth
    </div>
</nav>

<script>
    window.showAlert = function (message, icon = 'info', title = null) {
        if (!message) return Promise.resolve();

        if (!window.Swal) return Promise.resolve();

        return Swal.fire({
            title: title || (icon === 'error' ? 'Something went wrong' : 'Veripay'),
            text: message,
            icon,
            confirmButtonText: 'OK',
            confirmButtonColor: '#ff6863',
            customClass: {
                popup: 'swal2-store-popup',
                confirmButton: 'swal2-store-confirm',
            },
        });
    }

    window.confirmAction = function (message, title = 'Are you sure?') {
        if (!window.Swal) return Promise.resolve(false);

        return Swal.fire({
            title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ff6863',
            cancelButtonColor: '#68717d',
        }).then(result => result.isConfirmed);
    }

    window.getCookie = function (name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    window.redirectToLogin = function () {
        const current = window.location.pathname + window.location.search;
        window.location.href = `/login?redirect=${encodeURIComponent(current)}`;
    }

    window.apiFetch = async function (url, options = {}) {
        const redirectOnAuth = options.redirectOnAuth !== false;
        delete options.redirectOnAuth;

        await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
        const token = decodeURIComponent(window.getCookie('XSRF-TOKEN') || '');
        options.headers = Object.assign({
            'X-XSRF-TOKEN': token,
            'Accept': 'application/json',
        }, options.headers || {});
        options.credentials = 'include';
        const res = await fetch(url, options);
        const contentType = res.headers.get('content-type') || '';

        if ([401, 403, 419].includes(res.status)) {
            if (redirectOnAuth) {
                window.redirectToLogin();
            }

            throw new Error('Please sign in to continue.');
        }

        if (!contentType.includes('application/json')) {
            if (redirectOnAuth && res.redirected && res.url.includes('/login')) {
                window.redirectToLogin();
            }

            throw new Error('Please sign in to continue.');
        }

        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || 'Request failed');
        }
        return data;
    }

    window.updateCartBadge = async function () {
        const badge = document.getElementById('cart-count');
        if (!badge) return;

        try {
            const cart = await window.apiFetch('/api/cart', { redirectOnAuth: false });
            const count = (cart.items || []).reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        } catch (err) {
            badge.textContent = '0';
            badge.classList.add('hidden');
        }
    }

    window.animateCartFly = function (source) {
        const cart = document.getElementById('header-cart-link');
        if (!source || !cart) return;

        const start = source.getBoundingClientRect();
        const end = cart.getBoundingClientRect();
        const dot = document.createElement('span');
        dot.className = 'fly-dot';
        dot.style.left = `${start.left + start.width / 2}px`;
        dot.style.top = `${start.top + start.height / 2}px`;
        document.body.appendChild(dot);

        requestAnimationFrame(() => {
            dot.style.transform = `translate(${end.left - start.left}px, ${end.top - start.top}px) scale(0.45)`;
            dot.style.opacity = '0';
        });

        setTimeout(() => dot.remove(), 560);
    }

    window.addToCart = async function (productId, button = null, quantity = 1) {
        const original = button?.textContent;
        if (button) {
            button.disabled = true;
            button.textContent = 'Adding...';
        }

        try {
            await window.apiFetch('/api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity })
            });
            window.animateCartFly(button);
            await window.updateCartBadge();
            if (button) button.textContent = 'Added';
            setTimeout(() => {
                if (button) {
                    button.textContent = original || 'Add to cart';
                    button.disabled = false;
                }
            }, 900);
            return true;
        } catch (err) {
            if (button) {
                button.textContent = original || 'Add to cart';
                button.disabled = false;
            }
            window.showAlert(err.message || 'Unable to add item.', 'error');
            return false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            window.showAlert(@json(session('success')), 'success', 'Success');
        @endif
        @if(session('error'))
            window.showAlert(@json(session('error')), 'error');
        @endif

        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', async event => {
                event.preventDefault();
                const confirmed = await window.confirmAction(form.dataset.confirm || 'Continue with this action?');
                if (confirmed) form.submit();
            });
        });

        window.updateCartBadge();
        document.querySelectorAll('[data-add-to-cart]').forEach(button => {
            button.addEventListener('click', () => {
                window.addToCart(button.dataset.addToCart, button);
            });
        });
    });
</script>
@yield('scripts')
</body>
</html>
