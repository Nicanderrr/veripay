@extends('layouts.customer')

@section('title', 'Store Navigation')

@push('head')
    @vite('resources/js/store-navigation.js')
@endpush

@section('content')
<script id="store-navigation-data" type="application/json">@json($navigationData)</script>

<div class="store-nav-page">
    <section class="store-nav-stage shop-card">
        <div class="store-nav-canvas-wrap">
            <canvas id="store-nav-canvas" aria-label="3D store navigation map"></canvas>
            <div id="store-nav-fallback" class="store-nav-fallback hidden">
                WebGL is not available on this device. Use the route steps beside the map.
            </div>
            <div class="store-nav-overlay">
                <div>
                    <div class="section-kicker">Virtual Store</div>
                    <h1>3D Navigation</h1>
                </div>
                <div id="store-nav-status" class="store-nav-status">Ready</div>
            </div>
        </div>
    </section>

    <aside class="store-nav-panel">
        <div class="shop-card p-5">
            <div class="section-kicker">Find Product</div>
            <h2 class="store-nav-panel-title">Route setup</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="field-label" for="nav-product-search">Product search</label>
                    <input id="nav-product-search" class="shop-input mt-2" type="search" placeholder="Search by product or QR code">
                </div>

                <div>
                    <label class="field-label" for="nav-product">Destination</label>
                    <select id="nav-product" class="shop-select mt-2"></select>
                </div>

                <div>
                    <label class="field-label" for="nav-start">Current location</label>
                    <select id="nav-start" class="shop-select mt-2"></select>
                </div>

                <button id="nav-scan-checkpoint" class="shop-btn shop-btn-primary w-full" type="button">Scan Checkpoint QR</button>
            </div>
        </div>

        <div class="shop-card p-5">
            <div class="section-kicker">Directions</div>
            <h2 id="nav-destination-title" class="store-nav-panel-title">Select a product</h2>
            <div id="nav-route-summary" class="mt-2 text-sm font-semibold text-slate-500"></div>
            <ol id="nav-steps" class="store-nav-steps mt-4"></ol>
        </div>

        <div class="shop-card p-5">
            <div class="section-kicker">Checkpoint QR Codes</div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                @foreach($navigationData['checkpoints'] as $checkpoint)
                    <div class="store-nav-checkpoint">
                        <img src="{{ $checkpoint['qr'] }}" alt="Checkpoint QR for {{ $checkpoint['name'] }}">
                        <div>{{ $checkpoint['name'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
</div>

<div id="checkpoint-modal" class="checkpoint-modal hidden" aria-hidden="true">
    <div class="checkpoint-modal-panel">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="section-kicker">Checkpoint Scanner</div>
                <h2 class="store-nav-panel-title">Scan your current location</h2>
            </div>
            <button id="checkpoint-close" class="checkpoint-close" type="button" aria-label="Close scanner">x</button>
        </div>
        <div id="checkpoint-reader" class="mt-4 overflow-hidden rounded-lg bg-slate-950"></div>
        <div id="checkpoint-status" class="mt-3 rounded-lg bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600">Point camera at a checkpoint QR code.</div>
    </div>
</div>

<style>
    .store-nav-page {
        display: grid;
        gap: 18px;
    }

    .store-nav-stage {
        overflow: hidden;
    }

    .store-nav-canvas-wrap {
        position: relative;
        height: min(72vh, 720px);
        min-height: 460px;
        background: #f3f5f7;
    }

    #store-nav-canvas {
        display: block;
        width: 100%;
        height: 100%;
    }

    .store-nav-overlay {
        position: absolute;
        left: 18px;
        right: 18px;
        top: 18px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        pointer-events: none;
    }

    .store-nav-overlay h1 {
        margin: 2px 0 0;
        color: #1f2933;
        font-family: 'Raleway', Arial, sans-serif;
        font-size: 28px;
        font-weight: 700;
    }

    .store-nav-status {
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 9px 11px;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
    }

    .store-nav-panel {
        display: grid;
        gap: 18px;
    }

    .store-nav-panel-title {
        margin: 4px 0 0;
        color: #1f2933;
        font-family: 'Raleway', Arial, sans-serif;
        font-size: 22px;
        font-weight: 700;
    }

    .store-nav-steps {
        display: grid;
        gap: 10px;
        padding-left: 0;
        list-style: none;
    }

    .store-nav-steps li {
        display: grid;
        grid-template-columns: 28px 1fr;
        gap: 10px;
        align-items: start;
        color: #475569;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.45;
    }

    .store-nav-steps span {
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        border-radius: 4px;
        background: #fff1f0;
        color: #ff6863;
        font-size: 12px;
        font-weight: 900;
    }

    .store-nav-checkpoint {
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        background: #ffffff;
        padding: 10px;
        text-align: center;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
    }

    .store-nav-checkpoint img {
        width: 76px;
        height: 76px;
        margin: 0 auto 8px;
    }

    .store-nav-fallback {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        padding: 24px;
        color: #475569;
        text-align: center;
        font-weight: 800;
    }

    .checkpoint-modal {
        position: fixed;
        inset: 0;
        z-index: 150;
        display: grid;
        place-items: center;
        padding: 18px;
        background: rgba(15, 23, 42, 0.62);
    }

    .checkpoint-modal.hidden,
    .store-nav-fallback.hidden {
        display: none;
    }

    .checkpoint-modal-panel {
        width: min(100%, 520px);
        border-radius: 8px;
        border: 1px solid #e8e8e8;
        background: #ffffff;
        padding: 18px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
    }

    .checkpoint-close {
        width: 36px;
        height: 36px;
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        background: #ffffff;
        color: #475569;
        font-size: 18px;
        font-weight: 900;
    }

    @media (min-width: 1080px) {
        .store-nav-page {
            grid-template-columns: minmax(0, 1fr) 360px;
            align-items: start;
        }

        .store-nav-stage {
            position: sticky;
            top: 146px;
        }
    }

    @media (max-width: 760px) {
        .page-main {
            padding-bottom: 20px;
        }

        .bottom-nav {
            position: static;
            margin-top: 20px;
        }

        .store-nav-canvas-wrap {
            height: 58vh;
            min-height: 360px;
        }

        .store-nav-overlay {
            left: 12px;
            right: 12px;
            top: 12px;
        }

        .store-nav-overlay h1 {
            font-size: 22px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="/vendor/html5-qrcode.min.js"></script>
@endsection
