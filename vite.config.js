import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',         // permite acceso desde cualquier IP
        port: 5173,
        strictPort: true,
        hmr: {
            host: '192.168.0.123', // la IP de tu PC en red local
        },
    },
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
