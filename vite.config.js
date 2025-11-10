import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from "vite-plugin-static-copy";
import livewire from '@defstudio/vite-livewire-plugin';
import path from 'path';

export default defineConfig({
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        watch: {
            usePolling: true,
            interval: 300,
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '~': path.resolve(__dirname, './node_modules'),
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'resources/views/**',
                'app/Http/Controllers/**',
                'app/Livewire/**',
            ],
        }),
        livewire({
            refresh: [
                'resources/views/**',
                'app/Http/Controllers/**',
                'app/Livewire/**',
            ],
        }),
        viteStaticCopy({
            targets: [
                // Styles
                {
                    src: [
                        'node_modules/@tabler/core/dist/css/tabler.min.css',
                        'node_modules/@tabler/core/dist/css/tabler-flags.min.css',
                        'node_modules/@tabler/core/dist/css/tabler-payments.min.css',
                        'node_modules/@tabler/core/dist/css/tabler-vendors.min.css',
                        'node_modules/@tabler/core/dist/css/demo.min.css',
                        'node_modules/@tabler/core/dist/css/tabler-social.min.css',
                    ],
                    dest: '../dist/css'
                },
                // Scripts
                {
                    src: [
                        'node_modules/@tabler/core/dist/js/demo-theme.min.js',
                        'node_modules/@tabler/core/dist/js/tabler.min.js',
                        'node_modules/@tabler/core/dist/js/demo.min.js',
                    ],
                    dest: '../dist/js'
                },
                // libraries
                {
                    src: 'node_modules/@tabler/core/dist/libs/*',
                    dest: '../dist/libs'
                },
                // Images
                {
                    src: 'node_modules/@tabler/core/dist/img/*',
                    dest: '../dist/img'
                },
            ]
        })
    ],
});
