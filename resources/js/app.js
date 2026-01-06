import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'
import '../css/app.css';
import { registerSW } from 'virtual:pwa-register';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
        return pages[`./Pages/${name}.svelte`]
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props })
    },
})

// PWA service worker registration.
// To reduce "stuck navigation" after deploys (stale cached assets), auto-refresh when a new SW is available.
if (import.meta.env.PROD) {
    let updateSW;
    let didRefresh = false;
    updateSW = registerSW({
        immediate: true,
        onNeedRefresh() {
            // Hard refresh to ensure JS/CSS chunks are consistent.
            if (didRefresh) return;
            didRefresh = true;
            updateSW(true);
        },
    });
}
