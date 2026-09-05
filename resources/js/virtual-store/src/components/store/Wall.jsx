import { useBox } from '@react-three/cannon';
import { useTexture } from '@react-three/drei';
import * as THREE from 'three';
import { WALL_DIMENSIONS } from '../../lib/sceneConstants.js';

export default function Wall({ position, rotation = [0, 0, 0] }) {
    const texture = useTexture('/virtual-store/textures/wall.jpeg');
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.set(8, 1);

    const [ref] = useBox(() => ({
        args: WALL_DIMENSIONS,
        position,
        rotation,
        type: 'Static',
    }));

    return (
        <mesh ref={ref} position={position} rotation={rotation} castShadow receiveShadow>
            <boxGeometry args={WALL_DIMENSIONS} />
            <meshStandardMaterial map={texture} roughness={0.7} />
        </mesh>
    );
}
