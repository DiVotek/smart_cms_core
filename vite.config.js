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
      external: ['htmx.org', 'lazysizes'],
      rollupOptions: {
         input: {
            app: 'resources/js/app.js',
            htmx: 'resources/js/htmx.js',
            lazy: 'resources/js/lazy.js',
         },
         output: {
            entryFileNames: 'js/[name].js',
         }
      },
   },
});
