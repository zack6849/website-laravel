/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import "./bootstrap";
import "@fortawesome/fontawesome-free/js/fontawesome";
import {createApp} from "vue/dist/vue.esm-bundler.js"
import App from './components/App.vue'
import technologyComponent from "./components/TechnologyComponent.vue";
import photoGalleryComponent from "./components/PhotoGalleryComponent.vue";
import photoComponent from "./components/PhotoComponent.vue";
import projectCardComponent from "./components/ProjectCardComponent.vue";
import QSOMapComponent from "./components/QSOMapComponent.vue";

const app = createApp(App);
app.component('project-card', projectCardComponent);
app.component('photo-gallery', photoGalleryComponent);
app.component('technology', technologyComponent);
app.component('photo', photoComponent);
app.component('qso-map', QSOMapComponent);
app.mount('#app');

import Alpine from 'alpinejs'

window.Alpine = Alpine;
Alpine.start()
