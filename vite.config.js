import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/dashboard.css',
                'resources/css/pages/pos-checkout.css',
                'resources/css/pages/backup.css',
                'resources/css/pages/customers.css',
                'resources/css/pages/dashboard.css',
                'resources/css/pages/inventory.css',
                'resources/css/pages/reports.css',
                'resources/css/pages/sales.css',
                'resources/css/pages/users.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
