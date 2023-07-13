import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import inject from '@rollup/plugin-inject';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/index.js',
                // Page scripts loaded with Vite::asset() in the layouts
                'resources/js/scripts/ea-dashboard.js',
                'resources/js/scripts/ea-election.js',
                'resources/js/scripts/ea-add-election.js',
                'resources/js/scripts/ea-balance.js',
                'resources/js/scripts/ea-fee.js',
                'resources/js/scripts/ra-dashboard.js',
                'resources/js/scripts/ra-candidates.js',
                'resources/js/scripts/ra-addCandidate.js',
                'resources/js/scripts/veriff.js',
                'resources/js/scripts/vote.js',
                'resources/js/scripts/tally-home.js',
            ],
            refresh: true,
        }),
        inject({
            $: "jquery",
            jQuery: "jquery",
        })
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~': path.resolve(__dirname, 'node_modules')
        }
    }
});
