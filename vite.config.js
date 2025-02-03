import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
   plugins: [
      laravel({
         input: ['resources/js/app.js', 'resources/css/app.css'],
         publicDirectory: 'public',
         buildDirectory: 'public',
      }),
   ],
   build: {
      outDir: 'public',
      rollupOptions: {
         input: {
         },
         output: {
            entryFileNames: 'js/[name].js',
         }
      },
   },
});
