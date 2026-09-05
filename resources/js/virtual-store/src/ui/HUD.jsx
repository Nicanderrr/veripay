import { useCartStore } from '../stores/cartStore.js';
import { useProductStore } from '../stores/productStore.js';
import { usePlayerStore } from '../stores/playerStore.js';

const navItems = ['Home', 'Store Map', 'Search', 'Categories', 'Cart', 'Wishlist', 'Account', 'Settings'];

export default function HUD({ onClassicMode }) {
    const count = useCartStore((state) => state.count);
    const toggleCart = useCartStore((state) => state.toggleOpen);
    const setSearchOpen = useProductStore((state) => state.setSearchOpen);
    const toggleSettings = usePlayerStore((state) => state.toggleSettings);

    function handleItem(item) {
        if (item === 'Home') window.location.href = '/';
        if (item === 'Search' || item === 'Categories' || item === 'Store Map') setSearchOpen(true);
        if (item === 'Cart') toggleCart();
        if (item === 'Settings') toggleSettings();
        if (item === 'Account') window.location.href = '/login';
    }

    return (
        <div className="vs-hud">
            <div className="vs-brand">
                <span>Veripay</span>
                <small>Virtual Flagship</small>
            </div>
            <nav className="vs-hud-nav">
                {navItems.map((item) => (
                    <button key={item} type="button" onClick={() => handleItem(item)}>
                        {item}
                        {item === 'Cart' && count > 0 ? <b>{count}</b> : null}
                    </button>
                ))}
            </nav>
            <div className="vs-hud-actions">
                <button className="vs-enter-store" type="button">Enter Store</button>
                <button type="button" onClick={onClassicMode}>Classic Store</button>
            </div>
        </div>
    );
}
