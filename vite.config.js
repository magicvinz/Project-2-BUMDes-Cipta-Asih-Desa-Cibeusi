import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        cors: {
            origin: [
                'http://localhost:8000',
                'http://127.0.0.1:8000',
            ],
        },
    },
});

