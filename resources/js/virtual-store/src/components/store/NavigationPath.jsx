import { Line } from '@react-three/drei';
import { useProductStore } from '../../stores/productStore.js';

export default function NavigationPath() {
    const target = useProductStore((state) => state.navigationTarget);
    if (!target?.storePosition) return null;

    return (
        <Line
            points={[[0, 0.04, 6.8], [0, 0.04, 1.5], [target.storePosition.x, 0.04, 1.5], [target.storePosition.x, 0.04, target.storePosition.z]]}
            color="#ff6863"
            lineWidth={4}
        />
    );
}
