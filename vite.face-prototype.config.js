import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'storage/app/private/face-prototype-assets',
        emptyOutDir: false,
        rollupOptions: {
            input: 'resources/js/face-prototype.js',
            output: { entryFileNames: 'app.js', assetFileNames: 'app[extname]' },
        },
    },
});
