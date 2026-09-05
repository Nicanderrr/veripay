import { Text } from '@react-three/drei';

export default function CheckoutCounter() {
    return (
        <group position={[5.7, 0, 6.2]}>
            <mesh castShadow receiveShadow position={[0, 0.45, 0]}>
                <boxGeometry args={[3.4, 0.9, 1.05]} />
                <meshStandardMaterial color="#1f2933" roughness={0.22} metalness={0.18} />
            </mesh>
            <mesh castShadow position={[-0.9, 1.05, -0.1]}>
                <boxGeometry args={[0.8, 0.42, 0.12]} />
                <meshStandardMaterial color="#ffbf00" roughness={0.2} metalness={0.1} />
            </mesh>
            <Text position={[0, 1.12, 0.58]} fontSize={0.24} color="#ffffff" anchorX="center">
                CHECKOUT
            </Text>
        </group>
    );
}
