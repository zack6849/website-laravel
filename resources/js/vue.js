import {createApp} from "vue"
import App from './components/App.vue'
import technologyComponent from "./components/TechnologyComponent.vue";
import photoGalleryComponent from "./components/PhotoGalleryComponent.vue";
import photoComponent from "./components/PhotoComponent.vue";
import projectCardComponent from "./components/ProjectCardComponent.vue";
import QSOMapComponent from "./components/QSOMapComponent.vue";
import '../styles/main.scss'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css' // Ensure you import the MDI CSS

const vuetify = createVuetify({
    icons: {
        defaultSet: 'mdi',
    },
    components,
    directives
});

const app = createApp(App).use(vuetify);
app.component('project-card', projectCardComponent);
app.component('photo-gallery', photoGalleryComponent);
app.component('technology', technologyComponent);
app.component('photo', photoComponent);
app.component('qso-map', QSOMapComponent);
app.mount('#app');
