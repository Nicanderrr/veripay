import ProductShelf from './ProductShelf.jsx';

const sections = [
    { id: 'new-arrivals', label: 'New Arrivals', position: [-5, 0, -2], color: '#d8dde5' },
    { id: 'clothing', label: 'Clothing', position: [-1.7, 0, -2], color: '#f2f2f2' },
    { id: 'shoes', label: 'Shoes', position: [1.7, 0, -2], color: '#e6edf5' },
    { id: 'electronics', label: 'Electronics', position: [5, 0, -2], color: '#dfe7ea' },
];

export default function StoreEnvironment() {
    return (
        <group>
            <mesh receiveShadow position={[0, -0.05, 0]}>
                <boxGeometry args={[18, 0.1, 18]} />
                <meshStandardMaterial color="#f5f5f2" roughness={0.34} metalness={0.08} />
            </mesh>

            <mesh receiveShadow position={[0, 2.9, -9]}>
                <boxGeometry args={[18, 5.8, 0.18]} />
                <meshStandardMaterial color="#eceff3" roughness={0.48} />
            </mesh>
            <mesh receiveShadow position={[-9, 2.9, 0]}>
                <boxGeometry args={[0.18, 5.8, 18]} />
                <meshStandardMaterial color="#f7f7f7" roughness={0.42} />
            </mesh>
            <mesh receiveShadow position={[9, 2.9, 0]}>
                <boxGeometry args={[0.18, 5.8, 18]} />
                <meshStandardMaterial color="#f7f7f7" roughness={0.42} />
            </mesh>

            <mesh position={[0, 3.05, 0]}>
                <boxGeometry args={[18, 0.12, 18]} />
                <meshStandardMaterial color="#ffffff" roughness={0.3} />
            </mesh>

            <EntranceArea />

            {sections.map((section) => (
                <ProductShelf key={section.id} section={section} />
            ))}
        </group>
    );
}

function EntranceArea() {
    return (
        <group position={[0, 0, 7.5]}>
            <mesh receiveShadow>
                <boxGeometry args={[5.2, 0.08, 1.1]} />
                <meshStandardMaterial color="#ff6863" roughness={0.25} metalness={0.08} />
            </mesh>
            <mesh position={[0, 1.25, 0.72]}>
                <boxGeometry args={[4.8, 2.5, 0.08]} />
                <meshStandardMaterial color="#dfe8f2" roughness={0.05} metalness={0.1} transparent opacity={0.46} />
            </mesh>
        </group>
    );
}
