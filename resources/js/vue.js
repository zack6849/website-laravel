import { createApp } from "vue/dist/vue.esm-bundler";
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';
import App from "./components/App.vue";
import photoGalleryComponent from './components/PhotoGalleryComponent.vue';
import QSOMapComponent from './components/QSOMapComponent.vue';
import projectCardComponent from "./components/ProjectCardComponent.vue";
import technologyComponent from "./components/TechnologyComponent.vue";

const vuetify = createVuetify({
    icons: {
        defaultSet: 'mdi',
    },
    components,
    directives
});

const app = createApp(App).use(vuetify);
app.component('photo-gallery', photoGalleryComponent);
app.component('project-card', projectCardComponent);
app.component('technology', technologyComponent);
app.component('qso-map', QSOMapComponent);
app.mount('#app');
