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
            <div id="security-reader"></div>
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
<script src="/vendor/html5-qrcode.min.js"></script>
<script>
    const statusEl = document.getElementById('security-scan-status');
    const startBtn = document.getElementById('start-security-scan');
    const readerEl = document.getElementById('security-reader');
    const cameraSelect = document.getElementById('security-camera-select');
    let scanner = null;
    let scannerLibraryPromise = null;
    let lastScan = 0;
    let isStartingCamera = false;

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
        if (!scanner) {
            scanner = new Html5Qrcode('security-reader');
        }
        return scanner;
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
            statusEl.textContent = 'This QR code is not a Veripay receipt.';
            return;
        }

        statusEl.textContent = 'Receipt found. Opening verification...';
        window.location.href = url;
    }

    function scanConfig() {
        const boxSize = Math.max(210, Math.min(readerEl.clientWidth || 300, 320));

        const config = {
            fps: 12,
            qrbox: { width: boxSize, height: boxSize },
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
            if (scanner?.isScanning) {
                await scanner.stop();
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

        await scanner.start(
            selected.id,
            scanConfig(),
            onScanSuccess,
            () => { statusEl.textContent = 'Scanning...'; }
        );
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

            await ensureScanner();
            await stopScannerIfRunning();

            if (cameraId) {
                await scanner.start(
                    cameraId,
                    scanConfig(),
                    onScanSuccess,
                    () => { setStatus('Scanning...'); }
                );
            } else {
                try {
                    await scanner.start(
                        { facingMode: { exact: 'environment' } },
                        scanConfig(),
                        onScanSuccess,
                        () => { setStatus('Scanning...'); }
                    );
                } catch (rearError) {
                    try {
                        await scanner.start(
                            { facingMode: 'environment' },
                            scanConfig(),
                            onScanSuccess,
                            () => { setStatus('Scanning...'); }
                        );
                    } catch (environmentError) {
                        await startWithAvailableCamera();
                    }
                }
            }

            startBtn.classList.add('hidden');
            setStatus('Point the camera at the receipt QR code.', 'success');
            refreshCameraSelect();
        } catch (error) {
            setStatus('Camera failed to start. Please allow camera permission and try again. ' + error, 'error');
            startBtn.classList.remove('hidden');
        } finally {
            isStartingCamera = false;
            startBtn.disabled = false;
        }
    }

    startBtn.addEventListener('click', () => startScanner());

    if (!window.isSecureContext) {
        setStatus('Camera requires HTTPS. Open this page through your secure Hostinger domain.', 'error');
    } else {
        setStatus('Tap Start Scanner and allow camera permission.');
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
</script>
@endsection
