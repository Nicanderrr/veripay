import { useRef, useState } from 'react';
import { Physics } from '@react-three/cannon';
import { Html, SoftShadows } from '@react-three/drei';
import { EffectComposer, Bloom, SSAO } from '@react-three/postprocessing';
import Floor from './Floor.jsx';
import Wall from './Wall.jsx';
import Roof from './Roof.jsx';
import ShelvesGroup from './ShelvesGroup.jsx';
import FirstPersonPlayer from './FirstPersonPlayer.jsx';
import MobileControls from './MobileControls.jsx';
import CheckoutCounter from './CheckoutCounter.jsx';
import NavigationPath from './NavigationPath.jsx';
import { WALL_OFFSET_X, WALL_OFFSET_Z, WALL_POSITION_Y } from '../../lib/sceneConstants.js';
import { useProductStore } from '../../stores/productStore.js';
import { usePlayerStore } from '../../stores/playerStore.js';

export default function VirtualRoom() {
    const sceneRef = useRef(null);
    const [mobileMove, setMobileMove] = useState({ x: 0, y: 0 });
    const products = useProductStore((state) => state.products);
    const graphics = usePlayerStore((state) => state.graphics);

    return (
        <>
            <ambientLight intensity={0.42} />
            <hemisphereLight groundColor="#444444" intensity={0.62} />
            <directionalLight
                position={[10, 10, 5]}
                intensity={1.25}
                castShadow={graphics.shadows}
                shadow-mapSize-width={1024}
                shadow-mapSize-height={1024}
                shadow-camera-near={0.5}
                shadow-camera-far={32}
            />
            <rectAreaLight position={[0, 4.5, 2]} rotation={[-Math.PI / 2, 0, 0]} width={15} height={7} intensity={2.2} />
            <SoftShadows size={25} samples={8} focus={0.5} />
            <Physics gravity={[0, -9.81, 0]} iterations={10}>
                <FirstPersonPlayer sceneRef={sceneRef} mobileMove={mobileMove} />
                <group ref={sceneRef}>
                    <Roof />
                    <Floor />
                    <Wall position={[0, WALL_POSITION_Y, -WALL_OFFSET_Z]} />
                    <Wall position={[0, WALL_POSITION_Y, WALL_OFFSET_Z]} />
                    <Wall position={[-WALL_OFFSET_X, WALL_POSITION_Y, 0]} rotation={[0, Math.PI / 2, 0]} />
                    <Wall position={[WALL_OFFSET_X, WALL_POSITION_Y, 0]} rotation={[0, Math.PI / 2, 0]} />
                    <ShelvesGroup products={products} />
                    <CheckoutCounter />
                    <NavigationPath />
                </group>
            </Physics>
            {graphics.postProcessing && (
                <EffectComposer multisampling={0} resolutionScale={0.75}>
                    <SSAO samples={8} radius={0.05} intensity={14} />
                    <Bloom luminanceThreshold={0.5} luminanceSmoothing={0.9} height={260} />
                </EffectComposer>
            )}
            <Html fullscreen>
                <MobileControls setMove={setMobileMove} />
            </Html>
        </>
    );
}
