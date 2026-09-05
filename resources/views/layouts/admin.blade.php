<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Veripay Admin')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('rage-favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('rage-assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('rage-assets/css/animate.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700|Raleway:400,600,700|Open+Sans:300,400,600,700');

        :root {
            --admin-bg: #f7f7f7;
            --admin-surface: #ffffff;
            --admin-ink: #1f2933;
            --admin-muted: #68717d;
            --admin-line: #e8e8e8;
            --admin-primary: #ff6863;
            --admin-primary-strong: #ef4f4a;
            --admin-primary-soft: #fff1f0;
            --admin-charcoal: #202329;
            --admin-success: #16835b;
            --admin-warn: #b7791f;
            --admin-danger: #b91c1c;
            --admin-shadow: 0 14px 34px rgba(32, 35, 41, 0.08);
            --sidebar-w: 270px;
            --sidebar-collapsed-w: 88px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: 'Open Sans', Arial, sans-serif;
            background:
                linear-gradient(rgba(247, 247, 247, 0.94), rgba(247, 247, 247, 0.94)),
                url('{{ asset('rage-assets/images/background/header.jpg') }}') center top / cover fixed;
            color: var(--admin-ink);
        }

        body.admin-shell {
            display: block;
        }

        .admin-sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-w);
            background:
                linear-gradient(180deg, rgba(32, 35, 41, 0.96), rgba(32, 35, 41, 0.92)),
                url('{{ asset('rage-assets/images/background/header.jpg') }}') center / cover;
            color: #f8fafc;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 14px;
            z-index: 60;
            transition: width 200ms ease;
            overflow: hidden;
            box-shadow: 18px 0 44px rgba(32, 35, 41, 0.16);
        }

        .sidebar-scroll {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 104, 99, 0.55);
            border-radius: 999px;
        }

        body.admin-shell.sidebar-collapsed .admin-sidebar {
            width: var(--sidebar-collapsed-w);
        }

        .admin-sidebar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 44px 18px 10px;
            position: relative;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background: var(--admin-primary);
            display: grid;
            place-items: center;
            color: #fff;
            flex: 0 0 auto;
            box-shadow: 0 10px 22px rgba(255, 104, 99, 0.36);
        }

        .brand-text strong {
            display: block;
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0;
        }

        .brand-text span {
            display: block;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.66);
            text-transform: uppercase;
            letter-spacing: 0.18em;
        }

        body.admin-shell.sidebar-collapsed .brand-text,
        body.admin-shell.sidebar-collapsed .nav-label,
        body.admin-shell.sidebar-collapsed .sidebar-footer-text {
            display: none;
        }

        .sidebar-toggle {
            border: 0;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: grid;
            place-items: center;
            position: absolute;
            right: 8px;
            top: 10px;
            cursor: pointer;
            z-index: 3;
        }

        .admin-nav {
            display: grid;
            gap: 6px;
            margin-top: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 6px;
            color: rgba(255, 255, 255, 0.78);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: background 150ms ease, color 150ms ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .nav-link.active {
            background: var(--admin-primary);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(255, 104, 99, 0.22);
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
        }

        body.admin-shell.sidebar-collapsed .nav-link {
            justify-content: center;
            gap: 0;
            padding: 11px 0;
        }

        body.admin-shell.sidebar-collapsed .admin-sidebar .brand {
            justify-content: center;
            padding-right: 44px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.76);
            font-size: 12px;
            font-weight: 700;
            border-top: 3px solid var(--admin-primary);
        }

        .admin-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin-left 200ms ease;
        }

        body.admin-shell.sidebar-collapsed .admin-main {
            margin-left: var(--sidebar-collapsed-w);
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--admin-line);
            padding: 14px 26px;
        }

        .topbar-row {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 16px;
        }

        .page-title-wrap h1 {
            margin: 0;
            font-family: 'Raleway', Arial, sans-serif;
            font-size: 25px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: 0;
        }

        .page-title-wrap p {
            margin: 3px 0 0;
            color: var(--admin-primary);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .topbar-search {
            position: relative;
            max-width: 520px;
            justify-self: center;
            width: 100%;
        }

        .topbar-search input {
            width: 100%;
            border: 1px solid var(--admin-line);
            background: #f8fafc;
            border-radius: 6px;
            padding: 11px 16px 11px 42px;
            font-size: 14px;
            font-weight: 600;
            color: var(--admin-ink);
            outline: none;
        }

        .topbar-search svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--admin-muted);
            width: 17px;
            height: 17px;
        }

        .topbar-actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 1px solid var(--admin-line);
            background: #fff;
            display: grid;
            place-items: center;
            color: var(--admin-ink);
            cursor: pointer;
        }

        .profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--admin-line);
            background: #fff;
            border-radius: 6px;
            padding: 6px 12px 6px 6px;
            box-shadow: 0 8px 18px rgba(32, 35, 41, 0.05);
        }

        .logout-form {
            margin: 0;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 104, 99, 0.32);
            border-radius: 6px;
            background: var(--admin-primary);
            color: #ffffff;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(255, 104, 99, 0.2);
        }

        .logout-button:hover {
            background: var(--admin-primary-strong);
        }

        .logout-button svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            background: var(--admin-primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
        }

        .profile-meta {
            line-height: 1.15;
        }

        .profile-meta strong {
            display: block;
            font-size: 12px;
        }

        .profile-meta span {
            font-size: 11px;
            color: var(--admin-muted);
            font-weight: 700;
        }

        .admin-content {
            padding: 24px 26px 36px;
        }

        .admin-content-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: grid;
            gap: 16px;
            align-content: start;
        }

        .card {
            background: var(--admin-surface);
            border: 1px solid rgba(232, 232, 232, 0.95);
            border-radius: 6px;
            box-shadow: var(--admin-shadow);
            overflow: hidden;
        }

        .kpi-card {
            padding: 18px;
            position: relative;
            border-top: 4px solid var(--admin-primary);
        }

        .chart-card {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .chart-card-header {
            flex: 0 0 auto;
        }

        .chart-frame {
            position: relative;
            height: 320px;
            max-height: 320px;
            min-height: 320px;
            width: 100%;
            overflow: hidden;
        }

        .chart-frame.compact {
            height: 300px;
            max-height: 300px;
            min-height: 300px;
        }

        .chart-frame canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
            max-height: 100% !important;
        }

        .kpi-card .label {
            font-size: 12px;
            color: var(--admin-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .kpi-card .value {
            margin-top: 8px;
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 0;
            font-family: 'Montserrat', Arial, sans-serif;
        }

        .trend {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .trend.up {
            color: var(--admin-success);
            background: #e9f8f0;
        }

        .trend.down {
            color: var(--admin-danger);
            background: var(--admin-primary-soft);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            text-align: left;
            font-size: 12px;
            color: var(--admin-muted);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            padding: 14px 14px;
            border-bottom: 1px solid var(--admin-line);
            white-space: nowrap;
            background: #fbfbfb;
        }

        .admin-table td {
            padding: 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            vertical-align: middle;
        }

        .admin-table tbody tr:hover {
            background: #fff8f8;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .status-ok { background: #e9f8f0; color: var(--admin-success); }
        .status-warn { background: #fef3c7; color: var(--admin-warn); }
        .status-danger { background: #ffe8e7; color: var(--admin-danger); }
        .status-neutral { background: #f1f1f1; color: #39434f; }

        .btn-primary,
        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 800;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--admin-primary);
            color: #fff;
            box-shadow: 0 10px 20px rgba(255, 104, 99, 0.24);
        }

        .btn-primary:hover {
            background: var(--admin-primary-strong);
        }

        .btn-outline {
            background: #fff;
            color: #1f2937;
            border-color: var(--admin-line);
        }

        .btn-outline:hover {
            color: var(--admin-primary);
            background: var(--admin-primary-soft);
            border-color: rgba(255, 104, 99, 0.28);
        }

        .field,
        .select {
            width: 100%;
            border: 1px solid var(--admin-line);
            border-radius: 6px;
            background: #fff;
            color: var(--admin-ink);
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 600;
            outline: none;
        }

        .field:focus,
        .select:focus,
        .topbar-search input:focus {
            border-color: rgba(255, 104, 99, 0.55);
            box-shadow: 0 0 0 4px rgba(255, 104, 99, 0.12);
        }

        .admin-content .text-sky-700,
        .admin-content .text-sky-800 {
            color: var(--admin-primary) !important;
        }

        .admin-content .bg-sky-50,
        .admin-content .bg-slate-50 {
            background-color: #fbfbfb !important;
        }

        .admin-content .rounded-xl,
        .admin-content .rounded-2xl,
        .admin-content .rounded-lg {
            border-radius: 6px !important;
        }

        .admin-content .border-slate-200 {
            border-color: var(--admin-line) !important;
        }

        .security-scanner-frame {
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--admin-line);
            border-radius: 6px;
            background: #020617;
            padding: 8px;
        }

        #security-reader {
            width: 100%;
            min-height: 320px;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            border-radius: 6px;
            background: #020617;
        }

        #security-reader video,
        #security-reader canvas {
            width: 100% !important;
            height: 100% !important;
            min-height: 320px;
            object-fit: cover;
            border-radius: 6px;
            background: #020617;
            transform: translateZ(0);
        }

        #security-reader__scan_region,
        #security-reader__scan_region img {
            width: 100% !important;
        }

        #security-reader__dashboard {
            color: #ffffff;
            background: #111827;
            border: 0 !important;
            padding: 10px !important;
        }

        @media (max-width: 1100px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w);
            }

            body.admin-shell.mobile-sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            .admin-main,
            body.admin-shell.sidebar-collapsed .admin-main {
                margin-left: 0;
            }

            .topbar-row {
                grid-template-columns: auto 1fr auto;
            }
        }

        @media (max-width: 760px) {
            .admin-topbar {
                padding-inline: 14px;
            }

            .admin-content {
                padding-inline: 14px;
            }

            .topbar-search {
                display: none;
            }

            .logout-button span {
                display: none;
            }

            .logout-button {
                width: 42px;
                height: 42px;
                justify-content: center;
                padding: 0;
            }

            #security-reader,
            #security-reader video,
            #security-reader canvas {
                min-height: 280px;
            }
        }
    </style>
</head>
@php
    $current = request()->route()?->getName() ?? '';
@endphp
<body class="admin-shell">
<aside class="admin-sidebar">
    <div class="sidebar-scroll">
    <div class="brand">
        <span class="brand-logo">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M6 8h13l-1.4 8.2a2 2 0 0 1-2 1.8H9.1a2 2 0 0 1-2-1.6L5.3 4H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="brand-text">
            <strong>Veripay</strong>
            <span>Admin Console</span>
        </span>
        <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
    </div>

    <nav class="admin-nav">
        <a class="nav-link {{ $current === 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 12 12 4l9 8v8H3v-8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            <span class="nav-label">Dashboard</span>
        </a>
        <a class="nav-link {{ str_starts_with($current, 'admin.products') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16v13H4V7ZM8 4h8M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="nav-label">Products</span>
        </a>
        <a class="nav-link {{ str_starts_with($current, 'admin.categories') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z" stroke="currentColor" stroke-width="2"/></svg>
            <span class="nav-label">Categories</span>
        </a>
        <a class="nav-link {{ str_starts_with($current, 'admin.orders') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 7h14l-1.7 8.6a2 2 0 0 1-2 1.6H9.2a2 2 0 0 1-2-1.7L5.2 3.8H3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="nav-label">Orders</span>
        </a>
        <a class="nav-link {{ $current === 'admin.customers' ? 'active' : '' }}" href="{{ route('admin.customers') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="nav-label">Customers</span>
        </a>
        <a class="nav-link {{ $current === 'admin.inventory' ? 'active' : '' }}" href="{{ route('admin.inventory') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 8h16v11H4V8Zm3-4h10v4H7V4Zm3 7h4m-4 4h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="nav-label">Inventory</span>
        </a>
        <a class="nav-link {{ $current === 'admin.analytics' ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 20V4m5 16v-7m5 7V8m5 12V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="nav-label">Analytics</span>
        </a>
        <a class="nav-link {{ str_starts_with($current, 'security.') || $current === 'admin.security' ? 'active' : '' }}" href="{{ route('security.index') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.4 2.9 8.3 7 10 4.1-1.7 7-5.6 7-10V6l-7-3Zm-3 9 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="nav-label">Security</span>
        </a>
        <a class="nav-link {{ $current === 'admin.settings' ? 'active' : '' }}" href="{{ route('admin.settings') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM19.4 15a1 1 0 0 0 .2 1.1l.1.1a1 1 0 0 1 0 1.4l-1.2 1.2a1 1 0 0 1-1.4 0l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V21a1 1 0 0 1-1 1h-1.8a1 1 0 0 1-1-1v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a1 1 0 0 1-1.4 0l-1.2-1.2a1 1 0 0 1 0-1.4l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H3a1 1 0 0 1-1-1v-1.8a1 1 0 0 1 1-1h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1l-.1-.1a1 1 0 0 1 0-1.4l1.2-1.2a1 1 0 0 1 1.4 0l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9V3a1 1 0 0 1 1-1h1.8a1 1 0 0 1 1 1v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a1 1 0 0 1 1.4 0l1.2 1.2a1 1 0 0 1 0 1.4l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6H21a1 1 0 0 1 1 1v1.8a1 1 0 0 1-1 1h-.2a1 1 0 0 0-.9.6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
            <span class="nav-label">Settings</span>
        </a>
        <a class="nav-link {{ $current === 'password.change' ? 'active' : '' }}" href="{{ route('password.change') }}">
            <svg viewBox="0 0 24 24" fill="none"><path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v10H6V11Zm6 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="nav-label">Password</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-text">Live Ops</div>
        <div class="sidebar-footer-text">Queue + Analytics active</div>
    </div>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-topbar">
        <div class="topbar-row">
            <div class="page-title-wrap">
                <p>Admin Workspace</p>
                <h1>@yield('page_title', 'Dashboard')</h1>
            </div>
            <form class="topbar-search" method="GET" action="{{ route('admin.products.index') }}">
                <svg viewBox="0 0 24 24" fill="none"><path d="m21 21-4.2-4.2M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z" stroke="currentColor" stroke-width="2"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products by name or QR code...">
            </form>
            <div class="topbar-actions">
                <a class="icon-btn" href="{{ route('admin.inventory') }}" aria-label="Inventory alerts">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V10a6 6 0 1 0-12 0v4.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </a>
                <div class="profile-chip">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    <span class="profile-meta">
                        <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                        <span>{{ ucfirst(auth()->user()->role ?? 'admin') }}</span>
                    </span>
                </div>
                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit" aria-label="Logout">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M10 17l5-5-5-5M15 12H3m12-8h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <section class="admin-content">
        <div class="admin-content-inner">
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Success',
                            text: @json(session('success')),
                            icon: 'success',
                            confirmButtonColor: '#ff6863',
                        });
                    }
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Something went wrong',
                            text: @json(session('error')),
                            icon: 'error',
                            confirmButtonColor: '#ff6863',
                        });
                    }
                });
            </script>
        @endif

        @yield('content')
        </div>
    </section>
</main>

<script>
    (function () {
        window.showAlert = function (message, icon = 'info', title = null) {
            if (!message) return Promise.resolve();

            if (!window.Swal) return Promise.resolve();

            return Swal.fire({
                title: title || (icon === 'error' ? 'Something went wrong' : 'Veripay'),
                text: message,
                icon,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ff6863',
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

        const body = document.body;
        const toggle = document.getElementById('sidebar-toggle');
        const key = 'admin.sidebar.collapsed';
        const mobileBreakpoint = 1100;

        if (window.innerWidth > mobileBreakpoint && localStorage.getItem(key) === '1') {
            body.classList.add('sidebar-collapsed');
        }

        if (toggle) {
            toggle.addEventListener('click', () => {
                if (window.innerWidth <= mobileBreakpoint) {
                    body.classList.toggle('mobile-sidebar-open');
                    return;
                }

                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem(key, body.classList.contains('sidebar-collapsed') ? '1' : '0');
            });
        }

        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', async event => {
                event.preventDefault();
                const confirmed = await window.confirmAction(form.dataset.confirm || 'Continue with this action?');
                if (confirmed) form.submit();
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > mobileBreakpoint) {
                body.classList.remove('mobile-sidebar-open');
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        });
    })();
</script>
@yield('scripts')
</body>
</html>
