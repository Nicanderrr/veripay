import Shelf from './Shelf.jsx';
import { SHELF_SIZE, MAP_SCALE } from '../../lib/sceneConstants.js';

const sectionNames = ['New Arrivals', 'Clothing', 'Shoes', 'Accessories', 'Electronics', 'Best Sellers'];

export default function ShelvesGroup({ products }) {
    const rows = 3;
    const columns = 5;
    const shelfWidth = SHELF_SIZE[0];
    const shelfDepth = SHELF_SIZE[2];
    const startZ = 3;
    const rowSpacing = shelfDepth + 2.2 * MAP_SCALE;
    const columnSpacing = shelfWidth + 1.5 * MAP_SCALE;
    const productsPerShelf = Math.max(1, Math.ceil(products.length / (rows * columns)));

    return (
        <group>
            {Array.from({ length: rows }).map((_, rowIndex) => (
                Array.from({ length: columns }).map((__, colIndex) => {
                    const shelfIndex = rowIndex * columns + colIndex;
                    const start = shelfIndex * productsPerShelf;
                    const end = start + productsPerShelf;
                    const shelfProducts = products.slice(start, end);
                    const x = (colIndex - Math.floor(columns / 2)) * columnSpacing;
                    const z = startZ - rowIndex * rowSpacing;
                    const section = sectionNames[shelfIndex % sectionNames.length];

                    return (
                        <Shelf
                            key={`shelf-${rowIndex}-${colIndex}`}
                            products={shelfProducts}
                            position={[x, 0, z]}
                            rowIndex={rowIndex}
                            colIndex={colIndex}
                            section={section}
                        />
                    );
                })
            ))}
        </group>
    );
}
