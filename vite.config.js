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
            app: 'resources/js/app.js',
         },
         output: {
            entryFileNames: 'js/app.js',
         }
      },
   },
});
