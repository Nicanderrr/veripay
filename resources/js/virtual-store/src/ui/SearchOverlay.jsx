import { useProductStore } from '../stores/productStore.js';

export default function SearchOverlay() {
    const { searchOpen, products, searchTerm, setSearchOpen, setSearchTerm, setNavigationTarget, openProduct } = useProductStore();
    const matches = products.filter((product) => {
        const term = searchTerm.toLowerCase();
        return product.name.toLowerCase().includes(term)
            || product.category?.toLowerCase().includes(term)
            || product.barcode?.toLowerCase().includes(term);
    }).slice(0, 10);

    if (!searchOpen) return null;

    return (
        <div className="vs-overlay">
            <div className="vs-search-panel">
                <header>
                    <div>
                        <span>Search</span>
                        <h2>Find a product or section</h2>
                    </div>
                    <button type="button" onClick={() => setSearchOpen(false)}>Close</button>
                </header>
                <input autoFocus value={searchTerm} onChange={(event) => setSearchTerm(event.target.value)} placeholder="Search products, categories, barcodes..." />
                <div className="vs-search-results">
                    {matches.map((product) => (
                        <button key={product.id} type="button" onClick={() => {
                            setNavigationTarget(product);
                            openProduct(product);
                            setSearchOpen(false);
                        }}>
                            <strong>{product.name}</strong>
                            <span>{product.category} • GHS {Number(product.price || 0).toFixed(2)}</span>
                        </button>
                    ))}
                </div>
            </div>
        </div>
    );
}
