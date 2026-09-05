import { Suspense, useEffect, useState } from 'react';
import { Canvas } from '@react-three/fiber';
import { Loader } from '@react-three/drei';
import VirtualStore from './components/store/VirtualStore.jsx';
import HUD from './ui/HUD.jsx';
import CartDrawer from './ui/CartDrawer.jsx';
import SearchOverlay from './ui/SearchOverlay.jsx';
import SettingsPanel from './ui/SettingsPanel.jsx';
import ProductModal from './ui/ProductModal.jsx';
import ClassicStoreFallback from './ui/ClassicStoreFallback.jsx';
import CinematicLoader from './ui/CinematicLoader.jsx';
import { useProductStore } from './stores/productStore.js';
import { useCartStore } from './stores/cartStore.js';
import { usePlayerStore } from './stores/playerStore.js';

export default function VirtualStoreApp() {
    const [booting, setBooting] = useState(true);
    const [classicMode, setClassicMode] = useState(false);
    const loadProducts = useProductStore((state) => state.loadProducts);
    const syncCart = useCartStore((state) => state.syncCart);
    const graphics = usePlayerStore((state) => state.graphics);

    useEffect(() => {
        let active = true;

        Promise.allSettled([loadProducts(), syncCart()])
            .finally(() => {
                if (active) window.setTimeout(() => setBooting(false), 550);
            });

        return () => {
            active = false;
        };
    }, [loadProducts, syncCart]);

    if (booting) {
        return <CinematicLoader />;
    }

    if (classicMode) {
        return <ClassicStoreFallback onSwitchTo3d={() => setClassicMode(false)} />;
    }

    return (
        <div className="vs-shell">
            <div className="vs-canvas-wrap">
                <Canvas
                    shadows={graphics.shadows}
                    camera={{ position: [0, 1.75, 7], fov: 74, near: 0.1, far: 120 }}
                    gl={{ antialias: graphics.quality !== 'low', powerPreference: 'high-performance' }}
                >
                    <Suspense fallback={null}>
                        <VirtualStore />
                    </Suspense>
                </Canvas>
                <Loader />
            </div>

            <HUD onClassicMode={() => setClassicMode(true)} />
            <SearchOverlay />
            <CartDrawer />
            <SettingsPanel />
            <ProductModal />
        </div>
    );
}
