import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '127.0.0.1', // Force IPv4
        port: 5173,
        cors: true, // Allow cross-origin requests
        hmr: {
            host: 'localhost', // Force the browser to talk to localhost
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],
});