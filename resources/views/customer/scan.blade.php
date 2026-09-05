@extends('layouts.customer')

@section('title', 'Scan Products')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_340px]">
    <section class="shop-card relative overflow-hidden p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="section-kicker">Scan & Go</div>
                <h1 class="section-title mt-1">Scan products</h1>
                <p class="mt-2 max-w-xl text-sm font-semibold leading-6 text-slate-500">Point your camera at a product QR code to add products directly to your cart.</p>
            </div>
            <div id="scan-indicator" class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-extrabold text-emerald-700 opacity-0 transition-opacity">Scanned</div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <button id="start-scan" class="shop-btn shop-btn-primary" type="button">Start Camera</button>
            <select id="camera-select" class="shop-select hidden max-w-xs" aria-label="Switch camera"></select>
        </div>
        <div class="mt-5 overflow-hidden rounded-md border border-slate-200 bg-slate-950 p-2">
            <div id="reader" class="min-h-[320px] rounded-md bg-slate-900"></div>
        </div>
        <div id="scan-status" class="mt-4 rounded-md bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600"></div>
    </section>

    <aside class="space-y-4">
        <div class="shop-card p-5">
            <h2 class="text-xl font-black text-slate-950">Recent scan</h2>
            <div id="scan-result" class="mt-3 rounded-md bg-slate-50 p-4 text-sm font-bold text-slate-500">No scans yet.</div>
        </div>

        <div class="shop-card p-5">
            <h2 class="text-xl font-black text-slate-950">Tips</h2>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                <div class="flex gap-3"><span class="text-sky-700">1</span> Keep the QR code inside the scan box.</div>
                <div class="flex gap-3"><span class="text-sky-700">2</span> Avoid glare and hold steady.</div>
                <div class="flex gap-3"><span class="text-sky-700">3</span> Review your cart before checkout.</div>
            </div>
        </div>
    </aside>
</div>
@endsection

@section('scripts')
<script src="/vendor/html5-qrcode.min.js"></script>
<script>
    const statusEl = document.getElementById('scan-status');
    const resultEl = document.getElementById('scan-result');
    const startBtn = document.getElementById('start-scan');
    const cameraSelect = document.getElementById('camera-select');
    const readerEl = document.getElementById('reader');
    const indicatorEl = document.getElementById('scan-indicator');

    let lastScan = 0;

    function pulseIndicator() {
        if (!indicatorEl) return;
        indicatorEl.classList.remove('opacity-0');
        indicatorEl.classList.add('opacity-100');
        setTimeout(() => {
            indicatorEl.classList.add('opacity-0');
            indicatorEl.classList.remove('opacity-100');
        }, 900);
    }

    function onScanSuccess(decodedText) {
        const now = Date.now();
        if (now - lastScan < 1200) return; // throttle duplicate scans
        lastScan = now;

        statusEl.textContent = 'Scanned: ' + decodedText + '. Adding to cart...';
        resultEl.textContent = 'Last scanned: ' + decodedText;
        pulseIndicator();
        if (navigator.vibrate) navigator.vibrate(60);

        window.apiFetch('/api/scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(data => {
            resultEl.textContent = 'Added: ' + decodedText + '. Cart total: GHS ' + Number(data.total || 0).toFixed(2);
            statusEl.textContent = 'Item added to cart.';
        })
        .catch(err => {
            statusEl.textContent = err.toString();
        });
    }

    function onScanFailure() {
        if (!statusEl.textContent || statusEl.textContent.startsWith('Camera started')) {
            statusEl.textContent = 'Scanning...';
        }
    }

    let html5QrCode = null;
    let scannerLibraryPromise = null;
    let isStartingCamera = false;

    function loadScannerLibrary() {
        if (window.Html5Qrcode) return Promise.resolve();
        if (scannerLibraryPromise) return scannerLibraryPromise;

        scannerLibraryPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js';
            script.async = true;
            script.onload = () => window.Html5Qrcode ? resolve() : reject(new Error('Scanner library loaded without Html5Qrcode.'));
            script.onerror = () => reject(new Error('Scanner library could not load.'));
            document.head.appendChild(script);
        });

        return scannerLibraryPromise;
    }

    async function ensureScanner() {
        await loadScannerLibrary();
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }
        return html5QrCode;
    }

    function fillCameraSelect(devices) {
        if (!cameraSelect) return;

        cameraSelect.replaceChildren();
        devices.forEach((device, index) => {
            const option = document.createElement('option');
            option.value = device.id;
            option.textContent = device.label || `Camera ${index + 1}`;
            cameraSelect.appendChild(option);
        });

        cameraSelect.classList.toggle('hidden', devices.length <= 1);
    }

    function scanConfig() {
        const baseSize = Math.min(readerEl.clientWidth || 300, 360);
        const boxSize = Math.max(240, Math.floor(baseSize * 0.88));

        const config = {
            fps: 18,
            qrbox: { width: boxSize, height: boxSize },
            aspectRatio: 1.777,
            disableFlip: true,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true },
        };

        if (window.Html5QrcodeSupportedFormats?.QR_CODE) {
            config.formatsToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
        }

        return config;
    }

    function refreshCameraSelect() {
        loadScannerLibrary().then(() => Html5Qrcode.getCameras()).then(devices => {
            fillCameraSelect(devices || []);
        }).catch(() => {});
    }

    async function stopScannerIfRunning() {
        try {
            if (html5QrCode?.isScanning) {
                await html5QrCode.stop();
            }
        } catch (error) {
            // The scanner may already be stopped. Continue with the next start attempt.
        }
    }

    async function startWithAvailableCamera() {
        const devices = await Html5Qrcode.getCameras();
        fillCameraSelect(devices || []);

        if (!devices || !devices.length) {
            throw new Error('No camera found on this device.');
        }

        const rearCamera = devices.find(device => /back|rear|environment/i.test(device.label || ''));
        const selected = rearCamera || devices[devices.length - 1] || devices[0];
        if (cameraSelect && selected?.id) cameraSelect.value = selected.id;

        await html5QrCode.start(selected.id, scanConfig(), onScanSuccess, onScanFailure);
    }

    async function startCamera(cameraId = null) {
        if (isStartingCamera) return;
        isStartingCamera = true;
        startBtn.disabled = true;
        statusEl.textContent = 'Starting camera...';

        try {
            await ensureScanner();
            await stopScannerIfRunning();

            if (cameraId) {
                await html5QrCode.start(cameraId, scanConfig(), onScanSuccess, onScanFailure);
            } else {
                try {
                    await html5QrCode.start({ facingMode: { exact: 'environment' } }, scanConfig(), onScanSuccess, onScanFailure);
                } catch (rearError) {
                    try {
                        await html5QrCode.start({ facingMode: 'environment' }, scanConfig(), onScanSuccess, onScanFailure);
                    } catch (environmentError) {
                        await startWithAvailableCamera();
                    }
                }
            }

            statusEl.textContent = 'Camera started. Point at a product QR code.';
            startBtn.classList.add('hidden');
            refreshCameraSelect();
        } catch (error) {
            statusEl.textContent = 'Camera failed to start. Please allow camera permission and try again. ' + error;
            startBtn.classList.remove('hidden');
        } finally {
            isStartingCamera = false;
            startBtn.disabled = false;
        }
    }

    if (!window.isSecureContext) {
        statusEl.textContent = 'Camera requires HTTPS or localhost. Open this page over HTTPS or use a secure tunnel.';
    }

    startBtn.addEventListener('click', () => startCamera());
    if (cameraSelect) {
        cameraSelect.addEventListener('change', async () => {
            const nextCameraId = cameraSelect.value;
            statusEl.textContent = 'Switching camera...';

            try {
                if (html5QrCode?.isScanning) {
                    await html5QrCode.stop();
                }
                await startCamera(nextCameraId);
            } catch (error) {
                statusEl.textContent = 'Unable to switch camera. ' + error;
            }
        });
    }

    refreshCameraSelect();
</script>
@endsection
