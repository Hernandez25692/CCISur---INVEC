import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',       // Permite acceder desde fuera del contenedor
        port: 5173,            // Puerto por defecto de Vite
        strictPort: true,
        hmr: {
            host: 'localhost', // Host de tu navegador
        },
    },
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
