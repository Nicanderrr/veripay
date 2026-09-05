import React from 'react';
import { createRoot } from 'react-dom/client';
import VirtualStoreApp from './src/VirtualStoreApp.jsx';
import './src/styles/virtual-store.css';

const root = document.getElementById('virtual-store-root');

if (root) {
    createRoot(root).render(
        <React.StrictMode>
            <VirtualStoreApp />
        </React.StrictMode>
    );
}
