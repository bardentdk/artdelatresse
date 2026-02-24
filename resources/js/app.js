import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// 1. On importe le plugin Vue depuis le package qu'on vient d'installer
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Tresseuse App';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        app.use(plugin);
        
        // 2. On utilise le plugin (il détectera automatiquement les @routes de Blade)
        app.use(ZiggyVue);
        
        // Configuration de Toastify
        app.use(Vue3Toastify, {
            autoClose: 3000,
            position: 'bottom-right',
            hideProgressBar: true,
            closeOnClick: true,
            pauseOnHover: true,
        });

        app.mount(el);
    },
    progress: false,
});