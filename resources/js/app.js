import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

// @ts-ignore
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'USPA';

createInertiaApp({
    id: 'app',
    title: (title) => `${appName} - ${title}`,
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    resolve: (name) => {
        let parts = name.split('::')
        let type = false;
        if (parts.length > 1) type = parts[0]
        if(type) return require(`../../Modules/${type}/Resources/assets/js/Pages/${parts[1]}.vue`).default
        return require(`./Pages/${name}.vue`).default
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
})

