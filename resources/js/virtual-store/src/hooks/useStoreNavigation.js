import { useProductStore } from '../stores/productStore.js';

export function useStoreNavigation() {
    return {
        target: useProductStore((state) => state.navigationTarget),
        setTarget: useProductStore((state) => state.setNavigationTarget),
    };
}
