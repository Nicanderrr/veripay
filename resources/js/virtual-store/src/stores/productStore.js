import { create } from 'zustand';
import { api } from '../services/api.js';

export const useProductStore = create((set, get) => ({
    products: [],
    loading: false,
    error: null,
    activeProduct: null,
    selectedProduct: null,
    nearbyProduct: null,
    navigationTarget: null,
    searchOpen: false,
    searchTerm: '',
    async loadProducts() {
        if (get().loading || get().products.length) return;
        set({ loading: true, error: null });
        try {
            const payload = await api.get('/api/products?per_page=100');
            const products = (payload.data || payload).map(normalizeProduct);
            set({ products, navigationTarget: products[0] || null });
        } catch (error) {
            set({ error: error.message });
        } finally {
            set({ loading: false });
        }
    },
    openProduct(product) {
        set({ activeProduct: product, selectedProduct: product });
    },
    closeProduct() {
        set({ activeProduct: null });
    },
    setNavigationTarget(product) {
        set({ navigationTarget: product, selectedProduct: product });
    },
    setNearbyProduct(product) {
        set({ nearbyProduct: product, selectedProduct: product || get().selectedProduct });
    },
    setSearchOpen(searchOpen) {
        set({ searchOpen });
    },
    setSearchTerm(searchTerm) {
        set({ searchTerm });
    },
}));

function normalizeProduct(product, index) {
    const category = product.category?.name || product.category || 'Featured Products';

    return {
        id: product.id,
        name: product.name,
        barcode: product.barcode,
        category,
        description: product.description,
        shortDescription: product.short_description || product.description,
        price: Number(product.price || 0),
        salePrice: product.sale_price ? Number(product.sale_price) : null,
        stockQuantity: product.stock_quantity,
        image: product.image_path ? `/storage/${product.image_path}` : product.thumbnail,
        colors: product.colors || [],
        sizes: product.sizes || [],
        storePosition: product.store_position || fallbackPosition(index),
    };
}

function fallbackPosition(index = 0) {
    const section = Math.floor(index / 6);
    const slot = index % 6;
    return {
        x: -5 + section * 3.3 + (slot % 2) * 0.58,
        y: 0.45 + Math.floor(slot / 2) * 0.4,
        z: -3.85 + section * 0.15,
        rotationY: 0,
    };
}
