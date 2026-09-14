import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/admin.css', 'resources/js/admin.js', 'resources/css/sharedLayout.css', 'resources/css/pages/madeForHome.css', 'resources/css/pages/homeFaq.css', 'resources/css/pages/shop.css', 'resources/css/pages/product.css', 'resources/js/app.js', 'resources/js/pages/shop.js', 'resources/js/pages/product.js'],
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
