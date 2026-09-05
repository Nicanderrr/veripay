import { useProductStore } from '../stores/productStore.js';

export function useProductInteraction() {
    return {
        openProduct: useProductStore((state) => state.openProduct),
        closeProduct: useProductStore((state) => state.closeProduct),
    };
}
