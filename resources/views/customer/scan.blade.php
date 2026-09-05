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
            <div id="reader" class="relative min-h-[320px] overflow-hidden rounded-md bg-slate-900">
                <video id="scan-video" class="h-full min-h-[320px] w-full object-cover" muted playsinline webkit-playsinline></video>
                <canvas id="scan-canvas" class="hidden"></canvas>
                <div class="pointer-events-none absolute inset-[12%] rounded-xl border-2 border-white/80 shadow-[0_0_0_999px_rgba(2,6,23,0.36)]"></div>
            </div>
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
