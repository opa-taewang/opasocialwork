/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
// import jQuery from 'jquery';
// window.$ = jQuery;
require('./custom/jquery/jquery.min.js');
import './bootstrap';
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css'
// require('./maain.js');

// const app = createApp({
//     // root instance definition
// });

// require('./custom/bootstrap/bootstrap.min.js');
require('./custom/metismenu/metismenu.min.js');
require('./custom/simplebar/simplebar.min.js');
require('./custom/node-waves/node-waves.min.js');
require('./skote-starterkit.js');

// require('./custom/passwordModal.js');


/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */
// import { createApp } from 'vue';
import * as Vue from "vue";
import * as VueRouter from "vue-router";
// import  { createApp } from "vue";
import newOrder from "./components/newOrder.vue";
import autoSubscription from "./components/autoSubscription.vue";
import favouriteService from "./components/favouriteService.vue";
// Vue.component('example-componnent', require('./components/ExampleComponent.vue').default)

// createApp(app).mount("#layout-wrapper")

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
// './components/ExampleComponent.vue' -> <example-component></example-component>;
// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
const app = Vue.createApp();
app.component('newOrder', newOrder);
app.component('autoSubscription', autoSubscription);
app.component('favouriteService', favouriteService);
const mountedApp = app.mount('#vue_mount');
// import * as Vue from 'vue'
// window.Vue = require('vue');

// Vue.component('example-component', require('./components/favouriteService.vue').default);
