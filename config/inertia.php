<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Side Rendering
    |--------------------------------------------------------------------------
    |
    | These options configure if and how Inertia uses Server Side Rendering
    | to pre-render the initial visits made to your application's pages.
    |
    | This app does NOT run a persistent SSR node process (in ECS/Fargate or in
    | local docker-compose), so runtime SSR is disabled by default. Pages are
    | rendered client-side. The `vite build --ssr` bundle is still produced but
    | not used at runtime. Set INERTIA_SSR_ENABLED=true only if you also run
    | `php artisan inertia:start-ssr` alongside the app.
    |
    | https://inertiajs.com/server-side-rendering
    |
    */

    'ssr' => [
        'enabled' => env('INERTIA_SSR_ENABLED', false),
        'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    */

    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths' => [
            resource_path('js/Pages'),
        ],
        'page_extensions' => [
            'js',
            'svelte',
            'ts',
            'vue',
        ],
    ],

    'history' => [
        'encrypt' => false,
    ],

];
