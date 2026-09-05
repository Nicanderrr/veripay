import * as THREE from 'three';

const dataEl = document.getElementById('store-navigation-data');
const canvas = document.getElementById('store-nav-canvas');

if (dataEl && canvas) {
    const navData = JSON.parse(dataEl.textContent || '{}');
    const checkpoints = navData.checkpoints || [];
    const products = navData.products || [];

    const productSelect = document.getElementById('nav-product');
    const productSearch = document.getElementById('nav-product-search');
    const startSelect = document.getElementById('nav-start');
    const titleEl = document.getElementById('nav-destination-title');
    const summaryEl = document.getElementById('nav-route-summary');
    const stepsEl = document.getElementById('nav-steps');
    const statusEl = document.getElementById('store-nav-status');
    const fallbackEl = document.getElementById('store-nav-fallback');
    const scanBtn = document.getElementById('nav-scan-checkpoint');
    const modalEl = document.getElementById('checkpoint-modal');
    const closeBtn = document.getElementById('checkpoint-close');
    const checkpointStatus = document.getElementById('checkpoint-status');

    const checkpointById = new Map(checkpoints.map((checkpoint) => [checkpoint.id, checkpoint]));
    const raycaster = new THREE.Raycaster();
    const pointer = new THREE.Vector2();
    const productMeshes = new Map();
    const shelfMeshes = [];
    const routeObjects = new THREE.Group();
    const markerObjects = new THREE.Group();

    let selectedProducts = [...products];
    let routeLine = null;
    let userMarker = null;
    let destinationMarker = null;
    let traveler = null;
    let scanner = null;
    let scannerRunning = false;
    let cameraAngle = 0.2;
    let cameraDistance = 18;
    let dragging = false;
    let lastPointerX = 0;

    function option(label, value) {
        const el = document.createElement('option');
        el.value = value;
        el.textContent = label;
        return el;
    }

    function renderProductOptions(items) {
        productSelect.innerHTML = '';
        items.forEach((product) => {
            productSelect.appendChild(option(`${product.name} - ${product.category}`, product.id));
        });

        if (items.length && !items.some((product) => String(product.id) === productSelect.value)) {
            productSelect.value = items[0].id;
        }
    }

    function renderCheckpointOptions() {
        startSelect.innerHTML = '';
        checkpoints.forEach((checkpoint) => {
            startSelect.appendChild(option(checkpoint.name, checkpoint.id));
        });
        startSelect.value = checkpoints[0]?.id || '';
    }

    renderProductOptions(selectedProducts);
    renderCheckpointOptions();

    if (!window.WebGLRenderingContext) {
        fallbackEl?.classList.remove('hidden');
    }

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf3f5f7);
    scene.fog = new THREE.Fog(0xf3f5f7, 20, 42);

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: false });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);

    const ambient = new THREE.HemisphereLight(0xffffff, 0xd9dde3, 1.7);
    scene.add(ambient);

    const keyLight = new THREE.DirectionalLight(0xffffff, 1.9);
    keyLight.position.set(-7, 12, 8);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 1024;
    keyLight.shadow.mapSize.height = 1024;
    scene.add(keyLight);

    const floor = new THREE.Mesh(
        new THREE.BoxGeometry(19, 0.18, 16),
        new THREE.MeshStandardMaterial({ color: 0xfafafa, roughness: 0.72 })
    );
    floor.position.y = -0.1;
    floor.receiveShadow = true;
    scene.add(floor);

    const grid = new THREE.GridHelper(19, 19, 0xd8dee8, 0xe8edf3);
    grid.position.y = 0.01;
    scene.add(grid);

    function makeMat(color, roughness = 0.62) {
        return new THREE.MeshStandardMaterial({ color, roughness, metalness: 0.03 });
    }

    function makeTextSprite(text, color = '#1f2933') {
        const labelCanvas = document.createElement('canvas');
        labelCanvas.width = 512;
        labelCanvas.height = 128;
        const ctx = labelCanvas.getContext('2d');
        ctx.clearRect(0, 0, labelCanvas.width, labelCanvas.height);
        ctx.fillStyle = 'rgba(255,255,255,0.92)';
        ctx.strokeStyle = 'rgba(226,232,240,0.96)';
        ctx.lineWidth = 6;
        roundRect(ctx, 10, 18, 492, 92, 14);
        ctx.fill();
        ctx.stroke();
        ctx.fillStyle = color;
        ctx.font = '700 34px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, 256, 64, 460);

        const texture = new THREE.CanvasTexture(labelCanvas);
        texture.needsUpdate = true;
        const sprite = new THREE.Sprite(new THREE.SpriteMaterial({ map: texture, transparent: true }));
        sprite.scale.set(3.4, 0.85, 1);
        return sprite;
    }

    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    const aisles = [
        { id: 'produce', name: 'Fresh Produce', x: -6, z: -1.8, color: '#66b36b' },
        { id: 'beverages', name: 'Beverages', x: -2, z: -1.8, color: '#5d9cec' },
        { id: 'snacks', name: 'Snacks', x: 2, z: -1.8, color: '#f4b740' },
        { id: 'household', name: 'Household', x: 6, z: -1.8, color: '#9aa4b2' },
        { id: 'frozen', name: 'Frozen', x: 8, z: -4.8, color: '#72d6df' },
    ];

    aisles.forEach((aisle) => {
        const shelf = new THREE.Group();
        const base = new THREE.Mesh(new THREE.BoxGeometry(2.7, 1.5, 5.1), makeMat(aisle.color));
        base.position.set(aisle.x, 0.75, aisle.z);
        base.castShadow = true;
        base.receiveShadow = true;
        shelf.add(base);

        for (let i = 0; i < 4; i += 1) {
            const line = new THREE.Mesh(new THREE.BoxGeometry(2.88, 0.07, 5.2), makeMat(0xffffff, 0.5));
            line.position.set(aisle.x, 0.35 + i * 0.33, aisle.z);
            shelf.add(line);
        }

        const label = makeTextSprite(aisle.name);
        label.position.set(aisle.x, 2.05, aisle.z);
        shelf.add(label);
        shelfMeshes.push(shelf);
        scene.add(shelf);
    });

    function addZone(name, x, z, color) {
        const group = new THREE.Group();
        const mat = makeMat(color);
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(2.8, 0.18, 1.3), mat);
        mesh.position.set(x, 0.1, z);
        mesh.receiveShadow = true;
        group.add(mesh);
        const label = makeTextSprite(name);
        label.position.set(x, 0.75, z);
        group.add(label);
        scene.add(group);
    }

    addZone('Entrance', -8, 6, 0xff6863);
    addZone('Checkout', 0, 7, 0xffbf00);

    products.forEach((product) => {
        const marker = new THREE.Mesh(
            new THREE.SphereGeometry(0.15, 18, 18),
            new THREE.MeshStandardMaterial({ color: product.color || '#ff6863', emissive: product.color || '#ff6863', emissiveIntensity: 0.12 })
        );
        marker.position.set(product.x, 1.78, product.z);
        marker.userData.productId = product.id;
        scene.add(marker);
        productMeshes.set(String(product.id), marker);
    });

    scene.add(routeObjects);
    scene.add(markerObjects);

    function selectedProduct() {
        return products.find((product) => String(product.id) === String(productSelect.value)) || products[0];
    }

    function selectedCheckpoint() {
        return checkpointById.get(startSelect.value) || checkpoints[0];
    }

    function routePoints(start, product) {
        return [
            new THREE.Vector3(start.x, 0.08, start.z),
            new THREE.Vector3(start.x, 0.08, 4.1),
            new THREE.Vector3(product.x, 0.08, 4.1),
            new THREE.Vector3(product.x, 0.08, product.z + 1.6),
            new THREE.Vector3(product.x, 0.08, product.z),
        ];
    }

    function clearGroup(group) {
        while (group.children.length) {
            const child = group.children.pop();
            if (child.geometry) child.geometry.dispose();
            if (child.material) {
                if (Array.isArray(child.material)) child.material.forEach((mat) => mat.dispose());
                else child.material.dispose();
            }
        }
    }

    function buildRoute() {
        const product = selectedProduct();
        const start = selectedCheckpoint();
        if (!product || !start) return;

        clearGroup(routeObjects);
        clearGroup(markerObjects);

        const points = routePoints(start, product);
        const lineGeometry = new THREE.BufferGeometry().setFromPoints(points);
        const lineMaterial = new THREE.LineBasicMaterial({ color: 0xff6863, linewidth: 4 });
        routeLine = new THREE.Line(lineGeometry, lineMaterial);
        routeObjects.add(routeLine);

        const tube = new THREE.Mesh(
            new THREE.TubeGeometry(new THREE.CatmullRomCurve3(points), 80, 0.05, 10, false),
            new THREE.MeshStandardMaterial({ color: 0xff6863, emissive: 0xff6863, emissiveIntensity: 0.12 })
        );
        routeObjects.add(tube);

        for (let i = 1; i < points.length; i += 1) {
            const from = points[i - 1];
            const to = points[i];
            const direction = to.clone().sub(from);
            const length = direction.length();
            if (length < 0.5) continue;

            const arrowCount = Math.max(1, Math.floor(length / 2.2));
            for (let j = 1; j <= arrowCount; j += 1) {
                const arrow = new THREE.Mesh(
                    new THREE.ConeGeometry(0.18, 0.42, 24),
                    new THREE.MeshStandardMaterial({ color: 0xffbf00, emissive: 0xffbf00, emissiveIntensity: 0.16 })
                );
                const pos = from.clone().lerp(to, j / (arrowCount + 1));
                arrow.position.set(pos.x, 0.22, pos.z);
                arrow.rotation.x = Math.PI / 2;
                arrow.rotation.z = -Math.atan2(direction.z, direction.x) + Math.PI / 2;
                routeObjects.add(arrow);
            }
        }

        userMarker = new THREE.Mesh(
            new THREE.CylinderGeometry(0.28, 0.28, 0.42, 24),
            new THREE.MeshStandardMaterial({ color: 0x1f2933 })
        );
        userMarker.position.set(start.x, 0.31, start.z);
        markerObjects.add(userMarker);

        destinationMarker = new THREE.Mesh(
            new THREE.SphereGeometry(0.42, 32, 32),
            new THREE.MeshStandardMaterial({ color: 0x16a34a, emissive: 0x16a34a, emissiveIntensity: 0.45 })
        );
        destinationMarker.position.set(product.x, 2.05, product.z);
        markerObjects.add(destinationMarker);

        traveler = new THREE.Mesh(
            new THREE.SphereGeometry(0.16, 20, 20),
            new THREE.MeshStandardMaterial({ color: 0xffffff, emissive: 0xff6863, emissiveIntensity: 0.8 })
        );
        traveler.position.copy(points[0]);
        traveler.position.y = 0.35;
        markerObjects.add(traveler);

        productMeshes.forEach((mesh, id) => {
            mesh.scale.setScalar(id === String(product.id) ? 2.2 : 1);
            mesh.material.emissiveIntensity = id === String(product.id) ? 0.7 : 0.12;
        });

        updateSteps(start, product);
        statusEl.textContent = `${start.name} to ${product.name}`;
    }

    function estimateMeters(points) {
        let total = 0;
        for (let i = 1; i < points.length; i += 1) {
            total += points[i - 1].distanceTo(points[i]);
        }
        return Math.round(total * 1.4);
    }

    function updateSteps(start, product) {
        const points = routePoints(start, product);
        const meters = estimateMeters(points);
        titleEl.textContent = product.name;
        summaryEl.textContent = `${product.category} section • about ${meters} meters from ${start.name}`;
        const steps = [
            `Start at ${start.name}.`,
            'Follow the highlighted floor path toward the central aisle.',
            `Turn toward the ${product.category} shelves.`,
            `Stop at ${product.name}. Scan the product QR code to add it to cart.`,
        ];

        stepsEl.innerHTML = '';
        steps.forEach((step, index) => {
            const li = document.createElement('li');
            li.innerHTML = `<span>${index + 1}</span><div>${step}</div>`;
            stepsEl.appendChild(li);
        });
    }

    function resize() {
        const rect = canvas.parentElement.getBoundingClientRect();
        renderer.setSize(rect.width, rect.height, false);
        camera.aspect = rect.width / rect.height;
        camera.updateProjectionMatrix();
    }

    function updateCamera() {
        const x = Math.sin(cameraAngle) * cameraDistance;
        const z = Math.cos(cameraAngle) * cameraDistance;
        camera.position.set(x, 12, z);
        camera.lookAt(0, 0, 0.8);
    }

    function animate(time) {
        requestAnimationFrame(animate);
        const pulse = 1 + Math.sin(time * 0.006) * 0.1;
        if (destinationMarker) destinationMarker.scale.setScalar(pulse);

        if (traveler) {
            const product = selectedProduct();
            const start = selectedCheckpoint();
            const points = product && start ? routePoints(start, product) : [];
            if (points.length) {
                const cycle = (time * 0.00018) % 1;
                const segmentFloat = cycle * (points.length - 1);
                const segment = Math.min(points.length - 2, Math.floor(segmentFloat));
                const t = segmentFloat - segment;
                traveler.position.copy(points[segment].clone().lerp(points[segment + 1], t));
                traveler.position.y = 0.35;
            }
        }

        shelfMeshes.forEach((shelf) => {
            shelf.children.forEach((child) => {
                if (child.type === 'Sprite') child.lookAt(camera.position);
            });
        });

        renderer.render(scene, camera);
    }

    productSearch.addEventListener('input', () => {
        const term = productSearch.value.trim().toLowerCase();
        const current = productSelect.value;
        selectedProducts = products.filter((product) => {
            return product.name.toLowerCase().includes(term)
                || product.barcode.toLowerCase().includes(term)
                || product.category.toLowerCase().includes(term);
        });
        renderProductOptions(selectedProducts);
        if (selectedProducts.some((product) => String(product.id) === current)) {
            productSelect.value = current;
        }
        buildRoute();
    });

    productSelect.addEventListener('change', buildRoute);
    startSelect.addEventListener('change', buildRoute);

    canvas.addEventListener('pointerdown', (event) => {
        dragging = true;
        lastPointerX = event.clientX;
        canvas.setPointerCapture(event.pointerId);
    });

    canvas.addEventListener('pointermove', (event) => {
        if (!dragging) return;
        const delta = event.clientX - lastPointerX;
        lastPointerX = event.clientX;
        cameraAngle -= delta * 0.008;
        updateCamera();
    });

    canvas.addEventListener('pointerup', () => {
        dragging = false;
    });

    canvas.addEventListener('wheel', (event) => {
        event.preventDefault();
        cameraDistance = Math.min(26, Math.max(12, cameraDistance + event.deltaY * 0.01));
        updateCamera();
    }, { passive: false });

    canvas.addEventListener('click', (event) => {
        const rect = canvas.getBoundingClientRect();
        pointer.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        pointer.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(pointer, camera);
        const hits = raycaster.intersectObjects([...productMeshes.values()], false);
        if (!hits.length) return;
        const productId = hits[0].object.userData.productId;
        productSelect.value = productId;
        buildRoute();
    });

    async function stopScanner() {
        if (!scanner || !scannerRunning) return;
        await scanner.stop();
        scannerRunning = false;
    }

    function closeScanner() {
        stopScanner().catch(() => {});
        modalEl.classList.add('hidden');
        modalEl.setAttribute('aria-hidden', 'true');
    }

    async function openScanner() {
        modalEl.classList.remove('hidden');
        modalEl.setAttribute('aria-hidden', 'false');

        if (!window.Html5Qrcode) {
            checkpointStatus.textContent = 'Scanner library is not available.';
            return;
        }

        if (!scanner) scanner = new Html5Qrcode('checkpoint-reader');
        checkpointStatus.textContent = 'Point camera at a checkpoint QR code.';

        try {
            const devices = await Html5Qrcode.getCameras();
            if (!devices.length) {
                checkpointStatus.textContent = 'No camera found on this device.';
                return;
            }

            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 12,
                    qrbox: { width: 240, height: 240 },
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                },
                async (decodedText) => {
                    if (!decodedText.startsWith('checkpoint:')) {
                        checkpointStatus.textContent = 'This scanner only accepts location checkpoint QR codes.';
                        window.showAlert?.('This is a product code. Use the Scan page to add products to cart.', 'info');
                        return;
                    }

                    const checkpointId = decodedText.replace('checkpoint:', '').trim();
                    if (!checkpointById.has(checkpointId)) {
                        checkpointStatus.textContent = 'Unknown checkpoint.';
                        return;
                    }

                    startSelect.value = checkpointId;
                    buildRoute();
                    checkpointStatus.textContent = `Location set to ${checkpointById.get(checkpointId).name}.`;
                    await stopScanner();
                    setTimeout(closeScanner, 500);
                }
            );
            scannerRunning = true;
        } catch (error) {
            checkpointStatus.textContent = `Camera failed to start. ${error}`;
        }
    }

    scanBtn?.addEventListener('click', openScanner);
    closeBtn?.addEventListener('click', closeScanner);
    modalEl?.addEventListener('click', (event) => {
        if (event.target === modalEl) closeScanner();
    });

    window.addEventListener('resize', resize);
    resize();
    updateCamera();
    buildRoute();
    requestAnimationFrame(animate);
}
