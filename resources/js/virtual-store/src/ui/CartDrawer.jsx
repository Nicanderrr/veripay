import { useCartStore } from '../stores/cartStore.js';

export default function CartDrawer() {
    const { open, items, total, toggleOpen, updateItem, removeItem } = useCartStore();

    return (
        <aside className={`vs-drawer ${open ? 'is-open' : ''}`}>
            <header>
                <div>
                    <span>Cart</span>
                    <h2>Shopping Bag</h2>
                </div>
                <button type="button" onClick={toggleOpen}>Close</button>
            </header>
            <div className="vs-drawer-list">
                {items.length === 0 ? (
                    <p className="vs-empty">Your cart is empty.</p>
                ) : items.map((item) => (
                    <div className="vs-cart-item" key={item.id}>
                        {item.image_path ? <img src={`/storage/${item.image_path}`} alt="" /> : <div />}
                        <section>
                            <h3>{item.name}</h3>
                            <p>GHS {Number(item.unit_price || 0).toFixed(2)}</p>
                            <input type="number" min="1" value={item.quantity} onChange={(event) => updateItem(item, Number(event.target.value))} />
                            <button type="button" onClick={() => removeItem(item)}>Remove</button>
                        </section>
                    </div>
                ))}
            </div>
            <footer>
                <span>Subtotal</span>
                <strong>GHS {Number(total || 0).toFixed(2)}</strong>
                <a href="/checkout">Checkout</a>
            </footer>
        </aside>
    );
}
