import { usePlane } from '@react-three/cannon';
import { useTexture } from '@react-three/drei';
import * as THREE from 'three';
import { FLOOR_DIMENSIONS } from '../../lib/sceneConstants.js';

export default function Floor() {
    const texture = useTexture('/virtual-store/textures/floor-marble.jpg');
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.set(5, 4);

    const [ref] = usePlane(() => ({
        rotation: [-Math.PI / 2, 0, 0],
        type: 'Static',
    }));

    return (
        <mesh ref={ref} rotation={[-Math.PI / 2, 0, 0]} receiveShadow>
            <planeGeometry args={FLOOR_DIMENSIONS} />
            <meshStandardMaterial map={texture} roughness={0.42} metalness={0.08} />
        </mesh>
    );
}
