import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { globalMixins } from './globalMixins'; // Import the global mixins file
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// @ts-ignore
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'USPA';
const mainPages = import.meta.glob('./Pages/**/*.vue');
const modulePages = import.meta.glob('../../Modules/*/resources/assets/js/Pages/**/*.vue');

createInertiaApp({
    id: 'app',
    title: (title) => `${appName} - ${title}`,
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    resolve: (name) => {
        let parts = name.split('::')
        let type = false;
        if (parts.length > 1) type = parts[0]
        if (type) {
            return resolvePageComponent(
                `../../Modules/${type}/resources/assets/js/Pages/${parts[1]}.vue`,
                modulePages
            );
        }
        return resolvePageComponent(`./Pages/${name}.vue`, mainPages);
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);
        app.mixin(globalMixins);
        app.mount(el);
    },
})

