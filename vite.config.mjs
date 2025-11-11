// vite.config.mjs
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      // 1️⃣  pass an **array** of input files
      input: ['resources/css/app.css', 'resources/js/app.js'],

      // 2️⃣  ssr entry is fine on 0.8.x
      ssr: 'resources/js/ssr.js',

      refresh: true,
    }),

    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],

  // Define Vue feature flags
  define: {
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false),
    __VUE_OPTIONS_API__: JSON.stringify(true),
    __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
  },

  // (optional) nice path alias
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
});
