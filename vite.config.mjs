// vite.config.mjs
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            // 1️⃣  pass an **array** of input files
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],

            // 2️⃣  ssr entry is fine on 0.8.x
            ssr: 'resources/js/ssr.js',

            refresh: true
        }),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        })
    ],

    // (optional) nice path alias
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    }
});
