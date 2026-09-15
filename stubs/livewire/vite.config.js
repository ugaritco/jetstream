import { defineConfig } from 'vite';
import ugarit from 'ugarit-vite-plugin';

export default defineConfig({
    plugins: [
        ugarit({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
