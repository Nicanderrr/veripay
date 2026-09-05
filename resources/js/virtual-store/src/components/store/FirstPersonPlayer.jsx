import { useCallback, useEffect, useRef } from 'react';
import * as THREE from 'three';
import { useFrame } from '@react-three/fiber';
import { PerspectiveCamera, PointerLockControls } from '@react-three/drei';
import { useBox } from '@react-three/cannon';
import { isMobileDevice, useWASD } from '../../lib/input.js';
import { useProductStore } from '../../stores/productStore.js';

export default function FirstPersonPlayer({ sceneRef, mobileMove }) {
    const cameraRef = useRef(null);
    const raycaster = useRef(new THREE.Raycaster());
    const focusedProductRef = useRef(null);
    const setNearbyProduct = useProductStore((state) => state.setNearbyProduct);
    const openProduct = useProductStore((state) => state.openProduct);
    const controls = useWASD();

    const [ref, api] = useBox(() => ({
        mass: 1,
        args: [0.54, 1.85, 0.54],
        position: [0, 1.05, 7.2],
        linearDamping: 0.68,
        angularDamping: 1,
        fixedRotation: true,
    }));

    const dir = new THREE.Vector3();
    const rightVec = new THREE.Vector3();
    const up = new THREE.Vector3(0, 1, 0);

    useFrame(() => {
        if (!cameraRef.current) return;

        cameraRef.current.getWorldDirection(dir);
        dir.y = 0;
        dir.normalize();
        rightVec.crossVectors(up, dir).normalize();

        let mx = 0;
        let mz = 0;

        if (isMobileDevice()) {
            mx = -mobileMove.x;
            mz = mobileMove.y;
        } else {
            if (controls.forward()) mz += 1;
            if (controls.backward()) mz -= 1;
            if (controls.left()) mx += 1;
            if (controls.right()) mx -= 1;
        }

        const speed = isMobileDevice() ? 8.5 : (controls.sprint() ? 16 : 10.5);
        const moveDir = new THREE.Vector3();
        moveDir.addScaledVector(dir, mz);
        moveDir.addScaledVector(rightVec, mx);

        if (moveDir.lengthSq() > 0) {
            moveDir.normalize().multiplyScalar(speed);
        }

        api.velocity.set(moveDir.x, 0, moveDir.z);

        const product = findFocusedProduct(cameraRef.current, sceneRef.current, raycaster.current);
        if (focusedProductRef.current?.id !== product?.id) {
            focusedProductRef.current = product;
            setNearbyProduct(product);
        }
    });

    const interact = useCallback(() => {
        if (focusedProductRef.current) {
            openProduct(focusedProductRef.current);
        }
    }, [openProduct]);

    useEffect(() => {
        const handleKeyDown = (event) => {
            if (event.code === 'KeyE') interact();
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [interact]);

    useEffect(() => {
        if (!isMobileDevice() || !cameraRef.current) return undefined;

        let lastX = 0;
        let lastY = 0;
        let pitch = 0;
        let yaw = 0;

        const handleTouchStart = (event) => {
            if (event.target.closest('.vs-hud, .vs-drawer, .vs-product-modal, .vs-search-panel, .vs-settings, .vs-mobile-stick')) return;
            lastX = event.touches[0].clientX;
            lastY = event.touches[0].clientY;
        };

        const handleTouchMove = (event) => {
            if (event.target.closest('.vs-hud, .vs-drawer, .vs-product-modal, .vs-search-panel, .vs-settings, .vs-mobile-stick')) return;
            event.preventDefault();

            const touch = event.touches[0];
            const dx = touch.clientX - lastX;
            const dy = touch.clientY - lastY;
            lastX = touch.clientX;
            lastY = touch.clientY;

            yaw -= dx * 0.003;
            pitch = Math.max(-1.1, Math.min(1.1, pitch - dy * 0.003));
            cameraRef.current.rotation.set(pitch, yaw, 0, 'YXZ');
        };

        window.addEventListener('touchstart', handleTouchStart, { passive: false });
        window.addEventListener('touchmove', handleTouchMove, { passive: false });

        return () => {
            window.removeEventListener('touchstart', handleTouchStart);
            window.removeEventListener('touchmove', handleTouchMove);
        };
    }, []);

    return (
        <group ref={ref}>
            <PerspectiveCamera ref={cameraRef} makeDefault fov={74} position={[0, 0.65, 0]}>
                <group position={[0, 0, -1]}>
                    <mesh>
                        <planeGeometry args={[0.004, 0.05]} />
                        <meshBasicMaterial color="#ffffff" transparent opacity={0.8} />
                    </mesh>
                    <mesh>
                        <planeGeometry args={[0.05, 0.004]} />
                        <meshBasicMaterial color="#ffffff" transparent opacity={0.8} />
                    </mesh>
                </group>
            </PerspectiveCamera>
            {!isMobileDevice() && <PointerLockControls />}
        </group>
    );
}

function findFocusedProduct(camera, scene, raycaster) {
    if (!camera || !scene) return null;

    const cameraPos = camera.getWorldPosition(new THREE.Vector3());
    const cameraDir = camera.getWorldDirection(new THREE.Vector3());
    raycaster.set(cameraPos, cameraDir);
    raycaster.far = 3.2;

    const productObjects = [];
    scene.traverse((object) => {
        if (object.userData.product) productObjects.push(object);
    });

    const hits = raycaster.intersectObjects(productObjects, false);
    for (const hit of hits) {
        if (hit.object.userData.product) {
            return hit.object.userData.product;
        }
    }

    return null;
}
