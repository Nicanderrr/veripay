import { useCartStore } from '../stores/cartStore.js';
import { useProductStore } from '../stores/productStore.js';

export default function ProductModal() {
    const product = useProductStore((state) => state.activeProduct);
    const closeProduct = useProductStore((state) => state.closeProduct);
    const addItem = useCartStore((state) => state.addItem);

    if (!product) return null;

    return (
        <aside className="vs-product-modal">
            <button className="vs-modal-close" type="button" onClick={closeProduct}>Close</button>
            <div className="vs-modal-media">
                {product.image ? <img src={product.image} alt={product.name} /> : <span>{product.name.slice(0, 1)}</span>}
            </div>
            <section>
                <span>{product.category}</span>
                <h2>{product.name}</h2>
                <p>{product.shortDescription || product.description || 'Premium in-store product ready for self checkout.'}</p>
                <dl>
                    <div><dt>Price</dt><dd>GHS {Number(product.price || 0).toFixed(2)}</dd></div>
                    <div><dt>Sale</dt><dd>{product.salePrice ? `GHS ${Number(product.salePrice).toFixed(2)}` : 'N/A'}</dd></div>
                    <div><dt>Stock</dt><dd>{product.stockQuantity ?? 0}</dd></div>
                    <div><dt>Colors</dt><dd>{product.colors?.join(', ') || 'Default'}</dd></div>
                    <div><dt>Sizes</dt><dd>{product.sizes?.join(', ') || 'One size'}</dd></div>
                </dl>
                <div className="vs-modal-actions">
                    <button type="button" onClick={() => addItem(product)}>Add to Cart</button>
                    <a href="/checkout">Buy Now</a>
                    <button type="button">Wishlist</button>
                </div>
            </section>
        </aside>
    );
}
