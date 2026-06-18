import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
            assets: ['resources/images/**', 'resources/fonts/**'],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        viteStaticCopy({
            targets: [
                { src: 'resources/images/**/*', dest: '../images' },
                { src: 'resources/fonts/**/*', dest: '../fonts' },
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        },
        extensions: ['.js', '.vue'],
    },
});