import * as THREE from 'three';
import { ROOF_HEIGHT, ROOF_SIZE } from '../../lib/sceneConstants.js';

export default function Roof() {
    const [width, depth] = ROOF_SIZE;
    const spacing = 6.8;
    const positions = [];

    for (let x = -width / 2 + spacing; x < width / 2; x += spacing) {
        for (let z = -depth / 2 + spacing; z < depth / 2; z += spacing) {
            positions.push([x, z]);
        }
    }

    return (
        <group position={[0, ROOF_HEIGHT, 0]}>
            <mesh rotation={[Math.PI / 2, 0, 0]} receiveShadow>
                <planeGeometry args={ROOF_SIZE} />
                <meshStandardMaterial
                    color="#ffffff"
                    emissive="#f4f4f4"
                    emissiveIntensity={0.45}
                    roughness={0.44}
                    metalness={0.12}
                    side={THREE.DoubleSide}
                />
            </mesh>

            {positions.map(([x, z], index) => (
                <group key={`${x}-${z}-${index}`} position={[x, -0.5, z]}>
                    <mesh castShadow>
                        <cylinderGeometry args={[0.2, 0.25, 0.4, 18]} />
                        <meshStandardMaterial color="#151515" metalness={0.62} roughness={0.34} />
                    </mesh>
                    <mesh position={[0, -0.34, 0]}>
                        <sphereGeometry args={[0.14, 18, 18]} />
                        <meshStandardMaterial color="#ffffff" emissive="#ffffff" emissiveIntensity={1.8} roughness={0.2} />
                    </mesh>
                    <pointLight position={[0, -0.5, 0]} intensity={0.18} distance={6} color="#fff7ee" />
                </group>
            ))}
        </group>
    );
}
