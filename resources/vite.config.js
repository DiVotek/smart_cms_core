import tailwindcss from '@tailwindcss/vite';
import { existsSync, readdirSync, statSync } from 'fs';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import { defineConfig } from 'vite';


function getAssets() {
    let dir = resolve(__dirname, 'scms/templates/');
    const entries = readdirSync(dir);
    let files = [];
    for (const entry of entries) {
        let assetsPath = resolve(dir, entry) + '/assets';
        if (!existsSync(assetsPath) || !statSync(assetsPath).isDirectory()) {
            continue;
        }
        let jsPath = assetsPath + '/js';
        let cssPath = assetsPath + '/css';
        if (existsSync(jsPath) && statSync(jsPath).isDirectory()) {
            const js = resolve(dir, entry) + '/assets/js/app.js';
            if (existsSync(js) && statSync(js).isFile()) {
                files.push(js);
            }
        }

        if (existsSync(cssPath) && statSync(cssPath).isDirectory()) {
            const css = resolve(dir, entry) + '/assets/css/app.css';
            if (existsSync(css) && statSync(css).isFile()) {
                files.push(css);
            }
        }
    }
    return files;
}

export default defineConfig({
    plugins: [
        laravel({
            input: getAssets(),
            refresh: true,
        }),
        tailwindcss(),
    ],
});
