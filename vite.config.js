import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
   plugins: [
      laravel({
         input: ['resources/js/app.js', 'resources/css/app.css'],
         publicDirectory: 'public',
         buildDirectory: 'public',
      }),
      viteStaticCopy({
         targets: [
           {
             src: 'node_modules/htmx.org/dist/htmx.min.js',
             dest: 'js',
           },
         ],
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
