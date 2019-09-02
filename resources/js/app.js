/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require("./bootstrap");
require('@fortawesome/fontawesome-free/js/all');

window.Vue = require('vue');
Vue.component('project-card', require('./components/ProjectCardComponent.vue').default);
Vue.component('photo-gallery', require('./components/PhotoGalleryComponent').default);
Vue.component('photo', require('./components/PhotoComponent').default);
const app = new Vue({
    el: '#app',
    data: {
        shownav: false,
    },
    methods: {
        toggle() {
            this.shownav = !this.shownav
        }
    }
});
