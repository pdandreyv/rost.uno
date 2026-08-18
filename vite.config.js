import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'vendor/pdandreyv/rostpack/resources/css/rostpack.css',
                'vendor/pdandreyv/rostpack/resources/js/rostpack.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            }
        },
        manifest: true,
        emptyOutDir: true,
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
