import { useMemo } from 'react';
import * as THREE from 'three';
import { Text, useTexture } from '@react-three/drei';
import { useCompoundBody } from '@react-three/cannon';
import { mergeGeometries } from 'three/examples/jsm/utils/BufferGeometryUtils.js';
import ProductDisplay from './ProductDisplay.jsx';
import { PRODUCT_SIZE, SHELF_SIZE, SHELF_THICKNESS } from '../../lib/sceneConstants.js';

const SHELF_LEVELS = 3;
const SLOTS_PER_LEVEL = 5;

export default function Shelf({ products, position = [0, 0, 0], rowIndex = 0, colIndex = 0, section }) {
    const texture = useTexture('/virtual-store/textures/wood-light.jpeg');
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.set(2, 1);

    const [w, h, d] = SHELF_SIZE;
    const shelfSpacing = (h - SHELF_THICKNESS) / 3;

    const [ref] = useCompoundBody(() => ({
        mass: 0,
        position,
        shapes: [
            { type: 'Box', args: [w, SHELF_THICKNESS, d], position: [0, 0, 0] },
            { type: 'Box', args: [w, SHELF_THICKNESS, d], position: [0, shelfSpacing, 0] },
            { type: 'Box', args: [w, SHELF_THICKNESS, d], position: [0, 2 * shelfSpacing, 0] },
            { type: 'Box', args: [0.05, h, d], position: [-w / 2, h / 2, 0] },
            { type: 'Box', args: [0.05, h, d], position: [w / 2, h / 2, 0] },
            { type: 'Box', args: [w, h, 0.05], position: [0, h / 2, -d / 2] },
        ],
    }));

    const shelfGeometry = useMemo(() => {
        const geoms = [];
        Array.from({ length: SHELF_LEVELS }).forEach((_, i) => geoms.push(new THREE.BoxGeometry(w, SHELF_THICKNESS, d).translate(0, i * shelfSpacing, 0)));
        geoms.push(new THREE.BoxGeometry(0.05, h, d).translate(-w / 2, h / 2, 0));
        geoms.push(new THREE.BoxGeometry(0.05, h, d).translate(w / 2, h / 2, 0));
        geoms.push(new THREE.BoxGeometry(w, h, 0.05).translate(0, h / 2, -d / 2));
        return mergeGeometries(geoms, false);
    }, [w, h, d, shelfSpacing]);

    const placements = useMemo(() => {
        if (!products.length) return [];

        return Array.from({ length: SHELF_LEVELS * SLOTS_PER_LEVEL }).map((_, index) => {
            const product = products[index % products.length];
            const shelfIndex = Math.floor(index / SLOTS_PER_LEVEL);
            const indexOnLevel = index % SLOTS_PER_LEVEL;
            const y = shelfIndex * shelfSpacing + SHELF_THICKNESS + PRODUCT_SIZE / 2;
            const x = -w / 2 + (indexOnLevel + 0.5) * (w / SLOTS_PER_LEVEL);
            const z = d / 2 - PRODUCT_SIZE / 2 - 0.01;

            return { product, position: [x, y, z], slot: index };
        });
    }, [products, w, d, shelfSpacing]);

    return (
        <group ref={ref}>
            <mesh geometry={shelfGeometry} castShadow receiveShadow userData={{ isSolid: true }}>
                <meshStandardMaterial map={texture} color="#f8fafc" roughness={0.42} metalness={0.08} />
            </mesh>
            <Text position={[0, h + 0.28, d / 2 + 0.08]} fontSize={0.2} color="#1f2933" anchorX="center">
                {section}
            </Text>
            {placements.map(({ product, position: productPosition, slot }) => (
                <ProductDisplay
                    key={`prod-${rowIndex}-${colIndex}-${product.id}-${slot}`}
                    product={product}
                    position={productPosition}
                />
            ))}
        </group>
    );
}
