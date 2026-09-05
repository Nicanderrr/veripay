import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { api } from '../services/api.js';

export const useCartStore = create(persist((set, get) => ({
    open: false,
    items: [],
    total: 0,
    count: 0,
    toggleOpen() {
        set((state) => ({ open: !state.open }));
    },
    async syncCart() {
        if (!window.VeripayVirtualStore?.authenticated) {
            recalculateLocal(set, get().items);
            return;
        }

        try {
            const payload = await api.get('/api/cart', { redirectOnAuth: false });
            setFromCartPayload(set, payload);
        } catch {
            recalculateLocal(set, get().items);
        }
    },
    async addItem(product, quantity = 1) {
        if (!window.VeripayVirtualStore?.authenticated) {
            addLocalItem(set, get, product, quantity);
            return;
        }

        try {
            const payload = await api.post('/api/cart/add', { product_id: product.id, quantity });
            setFromCartPayload(set, payload);
        } catch {
            addLocalItem(set, get, product, quantity);
        }
    },
    updateItem(item, quantity) {
        const items = get().items.map((cartItem) => cartItem.id === item.id ? { ...cartItem, quantity: Math.max(1, quantity || 1) } : cartItem);
        recalculateLocal(set, items);
    },
    removeItem(item) {
        recalculateLocal(set, get().items.filter((cartItem) => cartItem.id !== item.id));
    },
}), {
    name: 'veripay-virtual-cart',
    partialize: (state) => ({ items: state.items, total: state.total, count: state.count }),
}));

function setFromCartPayload(set, payload) {
    const items = payload.items || [];
    set({
        items,
        total: Number(payload.total || 0),
        count: items.reduce((sum, item) => sum + Number(item.quantity || 0), 0),
    });
}

function recalculateLocal(set, items) {
    const total = items.reduce((sum, item) => sum + Number(item.unit_price || 0) * Number(item.quantity || 0), 0);
    const count = items.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
    set({ items, total, count });
}

function addLocalItem(set, get, product, quantity) {
    const existing = get().items.find((item) => item.product_id === product.id);
    const items = existing
        ? get().items.map((item) => item.product_id === product.id ? { ...item, quantity: item.quantity + quantity } : item)
        : [...get().items, {
            id: `local-${product.id}`,
            product_id: product.id,
            name: product.name,
            image_path: product.image?.replace('/storage/', ''),
            quantity,
            unit_price: product.price,
        }];

    recalculateLocal(set, items);
}
