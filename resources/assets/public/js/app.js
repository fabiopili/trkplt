/*
-----------------------------------------------------------------------
APP
-----------------------------------------------------------------------
*/

// Bootstrap
require('./bootstrap');

// Axios interceptors to handle the page loading indicator
axios.interceptors.request.use(function(config) {
	app.processing = true;
	return config;
});

// Mark processing as done on a successful response
axios.interceptors.response.use(function (response) {
	app.processing = false;
	return response
}, function (error) {
	app.processing = false;
	return false;
});

// Vue.js components
import appComponent from './components/appComponent.vue';
import resultComponent from './components/resultComponent.vue';
import notFoundComponent from './components/notFoundComponent.vue';
import csrf from './components/refreshCSRFTokenComponent.vue';
import loadingComponent from './components/loading.vue';

// Vue Router config
window.router = new VueRouter({
	mode: 'history',
	linkActiveClass: 'active',
	routes: [
		{
		path: '/',
			component: appComponent,
			name: 'home'
		},
		{
		path: '/result/:id',
			component: resultComponent,
			name: 'resume'
		},
		{
		path: '/*',
			component: notFoundComponent,
			name: 'notFound'
		}
	],
	scrollBehavior (to, from, savedPosition) {
		return { x: 0, y: 0 }
	}
});

// Init Vuex store
import { store } from './store'

// Vue.js base app and components
const app = new Vue({
	el: '#app',
	store,
	router: router,
	components: {
		appComponent,
		resultComponent,
		notFoundComponent,
		csrf,
		loadingComponent,
	},
	data: {
		processing: false,
		error: null
	},
	computed: {

	},
	mounted() {
	},
	watch: {
	    $route(to, from) {
	    }
	},
	methods: {

	}
});
