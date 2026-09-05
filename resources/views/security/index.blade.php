@extends('layouts.admin')

@section('page_title', 'Security Portal')

@section('content')
<div class="grid gap-4 xl:grid-cols-[1fr_380px]">
    <section class="card p-5">
        <div class="mb-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Exit verification</p>
            <h1 class="mt-1 text-2xl font-extrabold">Scan receipt QR</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button id="start-security-scan" class="btn-primary" type="button">Start Scanner</button>
            <select id="security-camera-select" class="field hidden max-w-xs" aria-label="Switch camera"></select>
        </div>
        <div class="security-scanner-frame mt-5">
            <div id="security-reader">
                <video id="security-video" muted playsinline webkit-playsinline></video>
                <canvas id="security-canvas" class="hidden"></canvas>
                <div class="security-scan-guide" aria-hidden="true"></div>
            </div>
        </div>
        <div id="security-scan-status" class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600"></div>
    </section>

    <aside class="card p-5">
        <h2 class="text-xl font-extrabold">Manual lookup</h2>
        <form method="POST" action="{{ route('security.lookup') }}" class="mt-4 grid gap-3">
            @csrf
            <label class="text-sm font-bold text-slate-600">Receipt token or QR URL</label>
            <textarea name="token" class="field" rows="5" required></textarea>
            <button class="btn-primary" type="submit">Verify Receipt</button>
        </form>
    </aside>
</div>
@endsection

@section('scripts')
<script>
    const localQrDecoderUrl = @json(asset('vendor/jsQR.js'));
    const cdnQrDecoderUrl = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
    const statusEl = document.getElementById('security-scan-status');
    const startBtn = document.getElementById('start-security-scan');
    const readerEl = document.getElementById('security-reader');
    const videoEl = document.getElementById('security-video');
    const canvasEl = document.getElementById('security-canvas');
    const canvasContext = canvasEl.getContext('2d', { willReadFrequently: true });
    const cameraSelect = document.getElementById('security-camera-select');
    let activeStream = null;
    let scanFrame = null;
    let nativeDetector = null;
    let lastScan = 0;
    let isStartingCamera = false;
    let isScanningFrame = false;
    let qrDecoderPromise = null;
    const isSafariBrowser = /^((?!chrome|android|crios|fxios).)*safari/i.test(navigator.userAgent) || /iPad|iPhone|iPod/.test(navigator.userAgent);

    function setStatus(message, tone = 'neutral') {
        statusEl.textContent = message;
        statusEl.className = 'mt-4 rounded-lg px-4 py-3 text-sm font-bold';
        if (tone === 'error') {
            statusEl.classList.add('bg-rose-50', 'text-rose-800');
            return;
        }
        if (tone === 'success') {
            statusEl.classList.add('bg-emerald-50', 'text-emerald-800');
            return;
        }
        statusEl.classList.add('bg-slate-50', 'text-slate-600');
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

    function receiptUrl(decodedText) {
        try {
            const url = new URL(decodedText);
            return url.pathname.includes('/security/receipts/') ? url.href : null;
        } catch (error) {
            return '{{ url('/security/receipts') }}/' + encodeURIComponent(decodedText.trim());
        }
    }

    function onScanSuccess(decodedText) {
        const now = Date.now();
        if (now - lastScan < 1500) return;
        lastScan = now;

        const url = receiptUrl(decodedText);
        if (!url) {
            setStatus('This QR code is not a Veripay receipt.', 'error');
            return;
        }

        setStatus('Receipt found. Opening verification...', 'success');
        stopScanner();
        window.location.href = url;
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
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = () => reject(new Error(`Unable to load ${src}`));
            document.head.appendChild(script);
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

    async function startScanner(cameraId = null) {
        if (isStartingCamera) return;
        isStartingCamera = true;
        startBtn.disabled = true;
        setStatus('Starting camera...');

        try {
            if (!window.isSecureContext) {
                throw new Error('Camera access requires HTTPS. Open the deployed security page with https://.');
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
            startBtn.classList.add('hidden');
            setStatus('Point the camera at the receipt QR code.', 'success');
        } catch (error) {
            stopScanner();
            setStatus('Camera failed to start. Please allow camera permission and try again. ' + error, 'error');
        } finally {
            isStartingCamera = false;
            startBtn.disabled = false;
        }
    }

    startBtn.addEventListener('click', () => startScanner());

    if (!window.isSecureContext) {
        setStatus('Camera requires HTTPS. Open this page through your secure Hostinger domain.', 'error');
    } else {
        setStatus(isSafariBrowser ? 'On Safari, tap Start Scanner and choose Allow when camera permission appears.' : 'Tap Start Scanner and allow camera permission.');
    }

    if (cameraSelect) {
        cameraSelect.addEventListener('change', async () => {
            setStatus('Switching camera...');
            try {
                await startScanner(cameraSelect.value);
            } catch (error) {
                setStatus('Unable to switch camera. ' + error, 'error');
            }
        });
    }

    refreshCameraSelect();
    window.addEventListener('pagehide', stopScanner);
    window.addEventListener('beforeunload', stopScanner);
</script>
@endsection
