import { readdirSync, statSync } from 'fs';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

function getFilesRecursive(dir, extensions = ['.js', '.css']) {
    const files = [];
    const entries = readdirSync(dir);

    for (const entry of entries) {
        const fullPath = resolve(dir, entry);
        if (statSync(fullPath).isDirectory()) {
            files.push(...getFilesRecursive(fullPath, extensions));
        } else if (extensions.some(ext => fullPath.endsWith(ext))) {
            files.push(fullPath);
        }
    }

    return files;
}

let inputFiles = getFilesRecursive(resolve(__dirname, 'scms/templates/'));
inputFiles = inputFiles.filter(file => !file.endsWith('tailwind.config.js'));

export default defineConfig({
    plugins: [
        laravel({
            input: inputFiles,
            refresh: true,
        }),
        tailwindcss(),
    ],
});
