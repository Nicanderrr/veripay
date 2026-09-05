import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const usePlayerStore = create(persist((set) => ({
    settingsOpen: false,
    graphics: {
        quality: 'medium',
        shadows: true,
        reflections: false,
        postProcessing: false,
    },
    toggleSettings() {
        set((state) => ({ settingsOpen: !state.settingsOpen }));
    },
    setQuality(quality) {
        set((state) => ({
            graphics: {
                ...state.graphics,
                quality,
                shadows: quality !== 'low',
                reflections: ['high', 'ultra'].includes(quality),
            },
        }));
    },
    toggleGraphics(key) {
        set((state) => ({
            graphics: {
                ...state.graphics,
                [key]: !state.graphics[key],
            },
        }));
    },
}), {
    name: 'veripay-graphics',
}));
