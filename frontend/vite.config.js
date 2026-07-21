import path from 'node:path';
import { fileURLToPath } from 'node:url';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig, loadEnv } from 'vite';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const backendEnvDir = path.resolve(__dirname, '../backend');

function redirectViteRootToLaravel() {
    return {
        name: 'redirect-vite-root-to-laravel',
        configureServer(server) {
            const env = loadEnv(server.config.mode, backendEnvDir, '');
            const appUrl = env.APP_URL || 'http://127.0.0.1:8000';

            server.middlewares.use((req, res, next) => {
                const url = req.url?.split('?')[0] ?? '';
                if (url === '/' || url === '/index.html') {
                    res.statusCode = 302;
                    res.setHeader('Location', appUrl);
                    res.end();
                    return;
                }
                next();
            });
        },
    };
}

export default defineConfig({
    envDir: backendEnvDir,
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: false,
    },
    plugins: [
        redirectViteRootToLaravel(),
        laravel({
            input: 'resources/js/app.tsx',
            ssr: 'resources/js/ssr.tsx',
            publicDirectory: '../public',
            buildDirectory: 'build',
            hotFile: '../public/hot',
            refresh: [
                '../backend/routes/**',
                '../backend/app/**',
                '../backend/resources/views/**',
            ],
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
