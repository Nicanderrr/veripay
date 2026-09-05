@extends('layouts.customer')

@section('title', 'Scan Products')

@push('head')
<style>
    .scan-page {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: start;
    }

    .scan-panel {
        overflow: hidden;
    }

    .scan-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 22px 0;
    }

    .scan-panel-copy {
        max-width: 620px;
    }

    .scan-panel-copy p {
        margin: 10px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        font-weight: 700;
    }

    .scan-indicator-pill {
        flex: 0 0 auto;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 900;
        transition: opacity 180ms ease;
    }

    .scan-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 18px 22px 0;
    }

    .scan-camera-select {
        max-width: 280px;
        min-width: 220px;
    }

    .scanner-shell {
        padding: 18px 22px 0;
    }

    .scanner-frame {
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 8px;
        background: radial-gradient(circle at top left, rgba(255, 104, 99, 0.24), transparent 30%), linear-gradient(135deg, #0f172a, #020617);
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.18);
    }

    .scanner-viewport {
        position: relative;
        aspect-ratio: 16 / 10;
        min-height: 330px;
        overflow: hidden;
        background: #020617;
    }

    .scanner-viewport video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #020617;
    }

    .scanner-guide {
        position: absolute;
        inset: 13%;
        border: 2px solid rgba(255, 255, 255, 0.88);
        border-radius: 14px;
        pointer-events: none;
        box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.38);
    }

    .scanner-guide::before,
    .scanner-guide::after {
        content: "";
        position: absolute;
        width: 34px;
        height: 34px;
        border-color: var(--shop-primary);
        border-style: solid;
    }

    .scanner-guide::before {
        top: -2px;
        left: -2px;
        border-width: 4px 0 0 4px;
        border-top-left-radius: 14px;
    }

    .scanner-guide::after {
        right: -2px;
        bottom: -2px;
        border-width: 0 4px 4px 0;
        border-bottom-right-radius: 14px;
    }

    .scanner-empty-state {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        padding: 24px;
        color: rgba(255, 255, 255, 0.74);
        text-align: center;
        font-size: 14px;
        font-weight: 800;
        pointer-events: none;
    }

    .scanner-viewport.has-feed .scanner-empty-state {
        display: none;
    }

    .scan-status {
        margin: 16px 22px 22px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        color: #475569;
        padding: 13px 15px;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.55;
    }

    .scan-side {
        display: grid;
        gap: 16px;
    }

    .scan-side-card {
        padding: 20px;
    }

    .scan-side-card h2 {
        margin: 0;
        color: #0f172a;
        font-size: 18px;
        font-weight: 900;
    }

    .scan-result-box {
        margin-top: 12px;
        min-height: 74px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        color: #64748b;
        padding: 13px;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.55;
    }

    .scan-tips {
        display: grid;
        gap: 11px;
        margin-top: 14px;
    }

    .scan-tip {
        display: grid;
        grid-template-columns: 26px minmax(0, 1fr);
        gap: 10px;
        align-items: start;
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.55;
    }

    .scan-tip span {
        display: grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 999px;
        background: var(--shop-soft);
        color: var(--shop-primary);
        font-size: 12px;
        font-weight: 900;
    }

    @media (max-width: 900px) {
        .scan-page {
            grid-template-columns: 1fr;
        }

        .scan-panel-header,
        .scan-controls,
        .scanner-shell {
            padding-inline: 16px;
        }

        .scan-status {
            margin-inline: 16px;
        }
    }

    @media (max-width: 560px) {
        .scan-panel-header {
            display: grid;
        }

        .scan-indicator-pill {
            width: fit-content;
        }

        .scanner-viewport {
            aspect-ratio: 4 / 5;
            min-height: 360px;
        }

        .scan-camera-select {
            max-width: none;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="scan-page">
    <section class="shop-card scan-panel">
        <div class="scan-panel-header">
            <div class="scan-panel-copy">
                <div class="section-kicker">Scan & Go</div>
                <h1 class="section-title mt-1">Scan products</h1>
                <p>Point your camera at a product QR code to add products directly to your cart.</p>
            </div>
            <div id="scan-indicator" class="scan-indicator-pill opacity-0">Scanned</div>
        </div>

        <div class="scan-controls">
            <button id="start-scan" class="shop-btn shop-btn-primary" type="button">Start Camera</button>
            <select id="camera-select" class="shop-select scan-camera-select hidden" aria-label="Switch camera"></select>
        </div>
        <div class="scanner-shell">
            <div class="scanner-frame">
                <div id="reader" class="scanner-viewport">
                    <video id="scan-video" muted playsinline webkit-playsinline></video>
                    <canvas id="scan-canvas" class="hidden"></canvas>
                    <div class="scanner-empty-state">Camera preview will appear here</div>
                    <div class="scanner-guide" aria-hidden="true"></div>
                </div>
            </div>
        </div>
        <div id="scan-status" class="scan-status"></div>
    </section>

    <aside class="scan-side">
        <div class="shop-card scan-side-card">
            <h2>Recent scan</h2>
            <div id="scan-result" class="scan-result-box">No scans yet.</div>
        </div>

        <div class="shop-card scan-side-card">
            <h2>Tips</h2>
            <div class="scan-tips">
                <div class="scan-tip"><span>1</span> Keep the QR code inside the scan box.</div>
                <div class="scan-tip"><span>2</span> Avoid glare and hold steady.</div>
                <div class="scan-tip"><span>3</span> Review your cart before checkout.</div>
            </div>
        </div>
    </aside>
</div>
@endsection

@section('scripts')
<script>
    const localQrDecoderUrl = @json(asset('vendor/jsQR.js'));
    const cdnQrDecoderUrl = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
    const statusEl = document.getElementById('scan-status');
    const resultEl = document.getElementById('scan-result');
    const startBtn = document.getElementById('start-scan');
    const cameraSelect = document.getElementById('camera-select');
    const readerEl = document.getElementById('reader');
    const videoEl = document.getElementById('scan-video');
    const canvasEl = document.getElementById('scan-canvas');
    const canvasContext = canvasEl.getContext('2d', { willReadFrequently: true });
    const indicatorEl = document.getElementById('scan-indicator');

    let lastScan = 0;
    let activeStream = null;
    let scanFrame = null;
    let nativeDetector = null;
    let isStartingCamera = false;
    let isScanningFrame = false;
    let qrDecoderPromise = null;
    const isSafariBrowser = /^((?!chrome|android|crios|fxios).)*safari/i.test(navigator.userAgent)
        || /iPad|iPhone|iPod/.test(navigator.userAgent);

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

    async function setupNativeDetector() {
        if (!('BarcodeDetector' in window) || nativeDetector) return;

        try {
            const supported = await BarcodeDetector.getSupportedFormats();
            if (supported.includes('qr_code')) {
                nativeDetector = new BarcodeDetector({ formats: ['qr_code'] });
            }
        } catch (error) {
            nativeDetector = null;
        }
    }

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const existing = document.querySelector(`script[src="${src}"]`);
            if (existing && window.jsQR) {
                resolve();
                return;
            }

            const script = existing || document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = () => reject(new Error(`Unable to load ${src}`));
            if (!existing) document.head.appendChild(script);
        });
    }

    async function ensureQrDecoder() {
        if (window.jsQR) return;
        if (!qrDecoderPromise) {
            qrDecoderPromise = loadScript(localQrDecoderUrl)
                .catch(() => loadScript(cdnQrDecoderUrl))
                .then(() => {
                    if (!window.jsQR) {
                        throw new Error('QR decoder did not initialize.');
                    }
                });
        }

        return qrDecoderPromise;
    }

    function fillCameraSelect(devices) {
        if (!cameraSelect) return;

        cameraSelect.replaceChildren();
        devices.forEach((device, index) => {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.textContent = device.label || `Camera ${index + 1}`;
            cameraSelect.appendChild(option);
        });

        cameraSelect.classList.toggle('hidden', devices.length <= 1);
    }

    async function availableCameras() {
        if (!navigator.mediaDevices?.enumerateDevices) return [];

        const devices = await navigator.mediaDevices.enumerateDevices();
        return devices.filter(device => device.kind === 'videoinput');
    }

    function preferredCamera(cameras) {
        if (!cameras.length) return null;

        return cameras.find(device => /back|rear|environment/i.test(device.label || ''))
            || cameras[cameras.length - 1]
            || cameras[0];
    }

    async function refreshCameraSelect() {
        try {
            const cameras = await availableCameras();
            fillCameraSelect(cameras);
        } catch (error) {
            fillCameraSelect([]);
        }
    }

    function drawVideoFrame() {
        const width = videoEl.videoWidth;
        const height = videoEl.videoHeight;
        if (!width || !height) return null;

        canvasEl.width = width;
        canvasEl.height = height;
        canvasContext.drawImage(videoEl, 0, 0, width, height);

        return canvasContext.getImageData(0, 0, width, height);
    }

    async function decodeFrame() {
        if (isScanningFrame || videoEl.readyState < HTMLMediaElement.HAVE_ENOUGH_DATA) return;
        isScanningFrame = true;

        try {
            if (nativeDetector) {
                const detected = await nativeDetector.detect(videoEl);
                const qr = detected.find(item => item.rawValue);
                if (qr?.rawValue) {
                    onScanSuccess(qr.rawValue);
                    return;
                }
            }

            if (window.jsQR) {
                const imageData = drawVideoFrame();
                if (!imageData) return;

                const qr = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert',
                });

                if (qr?.data) {
                    onScanSuccess(qr.data);
                }
            }
        } finally {
            isScanningFrame = false;
        }
    }

    function scanLoop() {
        decodeFrame().catch(() => {});
        scanFrame = window.setTimeout(scanLoop, 180);
    }

    function stopScanner() {
        if (scanFrame) {
            clearTimeout(scanFrame);
            scanFrame = null;
        }

        if (activeStream) {
            activeStream.getTracks().forEach(track => track.stop());
            activeStream = null;
        }

        videoEl.pause();
        videoEl.removeAttribute('src');
        videoEl.srcObject = null;
        videoEl.load();
        startBtn.classList.remove('hidden');
    }

    function cameraConstraints(cameraId = null) {
        const base = {
            width: { ideal: 1280 },
            height: { ideal: 720 },
        };

        if (cameraId) {
            return { audio: false, video: { ...base, deviceId: { exact: cameraId } } };
        }

        return { audio: false, video: { ...base, facingMode: { ideal: 'environment' } } };
    }

    async function openCamera(cameraId = null) {
        try {
            return await navigator.mediaDevices.getUserMedia(cameraConstraints(cameraId));
        } catch (error) {
            if (cameraId) throw error;
            return navigator.mediaDevices.getUserMedia({ audio: false, video: true });
        }
    }

    async function startCamera(cameraId = null) {
        if (isStartingCamera) return;
        isStartingCamera = true;
        startBtn.disabled = true;
        statusEl.textContent = 'Starting camera...';

        try {
            if (!window.isSecureContext) {
                throw new Error('Camera access requires HTTPS. Open the deployed scan page with https://.');
            }

            if (!navigator.mediaDevices?.getUserMedia) {
                throw new Error('This browser does not support camera scanning.');
            }

            stopScanner();

            await setupNativeDetector();
            if (!nativeDetector) {
                await ensureQrDecoder();
            }

            activeStream = await openCamera(cameraId);
            videoEl.srcObject = activeStream;
            videoEl.setAttribute('playsinline', 'true');
            videoEl.setAttribute('webkit-playsinline', 'true');
            videoEl.muted = true;
            await videoEl.play();

            await refreshCameraSelect();
            const currentTrack = activeStream.getVideoTracks()[0];
            const currentSettings = currentTrack?.getSettings ? currentTrack.getSettings() : {};
            if (cameraSelect && currentSettings.deviceId) {
                cameraSelect.value = currentSettings.deviceId;
            }

            scanLoop();
            statusEl.textContent = 'Camera started. Point at a product QR code.';
            startBtn.classList.add('hidden');
        } catch (error) {
            stopScanner();
            statusEl.textContent = 'Camera failed to start. Please allow camera permission and try again. ' + error;
            startBtn.classList.remove('hidden');
        } finally {
            isStartingCamera = false;
            startBtn.disabled = false;
        }
    }

    if (!window.isSecureContext) {
        statusEl.textContent = 'Camera requires HTTPS or localhost. Open this page over HTTPS or use a secure tunnel.';
    } else if (isSafariBrowser) {
        statusEl.textContent = 'On Safari, tap Start Camera and choose Allow when camera permission appears.';
    }

    startBtn.addEventListener('click', () => startCamera());
    if (cameraSelect) {
        cameraSelect.addEventListener('change', async () => {
            const nextCameraId = cameraSelect.value;
            statusEl.textContent = 'Switching camera...';

            try {
                await startCamera(nextCameraId);
            } catch (error) {
                statusEl.textContent = 'Unable to switch camera. ' + error;
            }
        });
    }

    refreshCameraSelect();
    window.addEventListener('pagehide', stopScanner);
    window.addEventListener('beforeunload', stopScanner);
</script>
@endsection
