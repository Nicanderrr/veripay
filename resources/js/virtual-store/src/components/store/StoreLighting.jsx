import { Environment } from '@react-three/drei';
import { usePlayerStore } from '../../stores/playerStore.js';

export default function StoreLighting() {
    const graphics = usePlayerStore((state) => state.graphics);

    return (
        <>
            <color attach="background" args={['#f3f4f6']} />
            <fog attach="fog" args={['#f3f4f6', 18, 48]} />
            <ambientLight intensity={0.75} />
            <directionalLight
                castShadow={graphics.shadows}
                position={[5, 9, 5]}
                intensity={2.1}
                shadow-mapSize={[1024, 1024]}
            />
            <rectAreaLight position={[0, 2.85, 2]} rotation={[-Math.PI / 2, 0, 0]} width={10} height={2.5} intensity={7} color="#ffffff" />
            <rectAreaLight position={[0, 2.85, -4]} rotation={[-Math.PI / 2, 0, 0]} width={10} height={2.5} intensity={5} color="#fff5ef" />
            {graphics.reflections && <Environment preset="city" />}
        </>
    );
}
