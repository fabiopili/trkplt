// Vue and Vue router
// https://vuejs.org/
// https://router.vuejs.org/installation.html
import Vue from 'vue'
import VueRouter from 'vue-router'

// Attach Vue and Vue Router
window.VueRouter = VueRouter;
Vue.use(VueRouter)

// Vuelidate
// https://vuelidate.js.org
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

// Attach Vue instance
window.Vue = Vue;

// Axios
// https://github.com/axios/axios
import axios from 'axios';
window.axios = axios;

// Axios base config
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrftoken = document.head.querySelector('meta[name="csrf-token"]');
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrftoken.content;
axios.defaults.withCredentials = true; // https://stackoverflow.com/questions/40941118/axios-wont-send-cookie-ajax-xhrfields-does-just-fine

// Lodash
var throttle = require('lodash/throttle');
window.throttle = throttle;

// Scroll events
import scrollEvents from './scrollEvents';
window.scrollEvents = scrollEvents;