import { Text } from '@react-three/drei';

export default function ProductShelf({ section }) {
    return (
        <group position={section.position}>
            <mesh castShadow receiveShadow position={[0, 0.65, 0]}>
                <boxGeometry args={[2.45, 1.3, 4.2]} />
                <meshStandardMaterial color={section.color} roughness={0.28} metalness={0.18} />
            </mesh>
            {[0.25, 0.65, 1.05].map((height) => (
                <mesh key={height} position={[0, height, 0]} castShadow>
                    <boxGeometry args={[2.62, 0.05, 4.32]} />
                    <meshStandardMaterial color="#ffffff" roughness={0.18} metalness={0.12} />
                </mesh>
            ))}
            <Text position={[0, 1.55, 2.34]} rotation={[-0.15, 0, 0]} fontSize={0.22} color="#1f2933" anchorX="center">
                {section.label}
            </Text>
        </group>
    );
}
