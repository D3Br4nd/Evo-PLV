import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

const hmrHost = process.env.VITE_HMR_HOST;
const hmrProtocol = process.env.VITE_HMR_PROTOCOL || 'wss';
const hmrClientPort = process.env.VITE_HMR_CLIENT_PORT
    ? Number(process.env.VITE_HMR_CLIENT_PORT)
    : undefined;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            ssr: 'resources/js/ssr.js', // Optional for Inertia SSR
        }),
        svelte(),
        tailwindcss(),
        VitePWA({
            registerType: 'autoUpdate',
            manifest: {
                name: 'Pro Loco Venticanese',
                short_name: 'PLV Evolution',
                description: 'L\'evoluzione della tradizione.',
                // PWA should land on the mobile member home.
                start_url: '/me',
                scope: '/',
                display: 'standalone',
                theme_color: '#000000',
                icons: [
                    {
                        src: 'pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: 'pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,webmanifest}'],
            },
        })
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // Enable CORS for dev server usage behind reverse proxy (HMR/dev only).
        cors: true,
        ...(hmrHost
            ? {
                  hmr: {
                      host: hmrHost,
                      protocol: hmrProtocol,
                      clientPort: hmrClientPort,
                  },
              }
            : {}),
        watch: {
            usePolling: true,
        },
    },
});
