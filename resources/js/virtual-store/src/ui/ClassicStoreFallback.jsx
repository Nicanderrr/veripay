import { useProductStore } from '../stores/productStore.js';

export default function ClassicStoreFallback({ onSwitchTo3d }) {
    const products = useProductStore((state) => state.products);

    return (
        <div className="vs-classic">
            <header>
                <div>
                    <span>Classic Store</span>
                    <h1>Accessible product view</h1>
                </div>
                <button type="button" onClick={onSwitchTo3d}>Return to 3D Store</button>
            </header>
            <div className="vs-classic-grid">
                {products.map((product) => (
                    <a key={product.id} href={`/products/${product.id}`}>
                        {product.image ? <img src={product.image} alt={product.name} /> : <div />}
                        <strong>{product.name}</strong>
                        <span>GHS {Number(product.price || 0).toFixed(2)}</span>
                    </a>
                ))}
            </div>
        </div>
    );
}
