import { useEffect, useRef } from 'react';

export function isMobileDevice() {
    if (typeof navigator === 'undefined') return false;
    return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
}

export function useWASD() {
    const keysRef = useRef({});

    useEffect(() => {
        const down = (event) => {
            keysRef.current[event.code] = true;
        };
        const up = (event) => {
            keysRef.current[event.code] = false;
        };

        window.addEventListener('keydown', down);
        window.addEventListener('keyup', up);

        return () => {
            window.removeEventListener('keydown', down);
            window.removeEventListener('keyup', up);
        };
    }, []);

    const is = (code) => Boolean(keysRef.current[code]);

    return {
        forward: () => is('KeyW') || is('ArrowUp'),
        backward: () => is('KeyS') || is('ArrowDown'),
        left: () => is('KeyA') || is('ArrowLeft'),
        right: () => is('KeyD') || is('ArrowRight'),
        sprint: () => is('ShiftLeft') || is('ShiftRight'),
    };
}
