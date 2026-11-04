import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/demo1.css',
                'resources/css/demo2.css',
                'resources/css/demo3.css',
                'resources/js/global.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'http://localhost:5173',
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/**', '**/vendor/**', '**/public/**', '**/tests/**', '**/.git/**', '**/node_modules/**'],
            // Docker Desktop on Windows/macOS does not forward file-change events
            // from bind mounts, so HMR needs polling there (set in docker-compose.dev.yml).
            usePolling: process.env.CHOKIDAR_USEPOLLING === 'true',
            interval: 1000,
        },
    },
});
