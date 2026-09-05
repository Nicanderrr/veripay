import { Suspense, useRef } from 'react';
import { useFrame } from '@react-three/fiber';
import { Html, useTexture } from '@react-three/drei';
import * as THREE from 'three';
import { useProductStore } from '../../stores/productStore.js';
import { PRODUCT_DIMENSIONS } from '../../lib/sceneConstants.js';

export default function ProductDisplay({ product, position }) {
    const mesh = useRef();
    const openProduct = useProductStore((state) => state.openProduct);
    const nearbyProduct = useProductStore((state) => state.nearbyProduct);
    const isSelected = nearbyProduct?.id === product.id;

    useFrame((_, delta) => {
        if (mesh.current && isSelected) {
            mesh.current.rotation.y += delta * 0.8;
        }
    });

    return (
        <group position={position}>
            <Suspense fallback={<FallbackProductBox product={product} isSelected={isSelected} mesh={mesh} />}>
                {product.image ? (
                    <TexturedProductBox product={product} isSelected={isSelected} mesh={mesh} />
                ) : (
                    <FallbackProductBox product={product} isSelected={isSelected} mesh={mesh} />
                )}
            </Suspense>
            {isSelected && (
                <Html center position={[0, 0.58, 0.08]} distanceFactor={7}>
                    <div className="vs-interaction-pill">
                        <span className="vs-desktop-only">Press E to View</span>
                        <button className="vs-mobile-only" type="button" onClick={() => openProduct(product)}>View Product</button>
                    </div>
                </Html>
            )}
        </group>
    );
}

function TexturedProductBox({ product, isSelected, mesh }) {
    const texture = useTexture(product.image);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = 4;

    return (
        <mesh
            ref={mesh}
            castShadow
            receiveShadow
            onClick={() => useProductStore.getState().openProduct(product)}
            userData={{ product }}
            scale={isSelected ? [1.18, 1.18, 1.18] : [1, 1, 1]}
        >
            <boxGeometry args={PRODUCT_DIMENSIONS} />
            <meshStandardMaterial
                map={texture}
                color="#ffffff"
                roughness={0.34}
                metalness={0.22}
                emissive={isSelected ? '#ff6863' : '#000000'}
                emissiveIntensity={isSelected ? 0.3 : 0}
            />
        </mesh>
    );
}

function FallbackProductBox({ product, isSelected, mesh }) {
    return (
        <mesh
            ref={mesh}
            castShadow
            receiveShadow
            onClick={() => useProductStore.getState().openProduct(product)}
            userData={{ product }}
            scale={isSelected ? [1.18, 1.18, 1.18] : [1, 1, 1]}
        >
            <boxGeometry args={PRODUCT_DIMENSIONS} />
            <meshStandardMaterial
                color={isSelected ? '#ff6863' : '#ffffff'}
                roughness={0.34}
                metalness={0.22}
                emissive={isSelected ? '#ff6863' : '#000000'}
                emissiveIntensity={isSelected ? 0.3 : 0}
            />
        </mesh>
    );
}
