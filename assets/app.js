import './styles/app.css';

// start the Stimulus application
// import './bootstrap';

import Vue from "vue";
import VueRouter from "vue-router";
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import Home from "./views/Home";
import App from "./views/App";

const routes = [
    { path: '/', component: Home, name: 'home'}
];

const router = new VueRouter({
    mode: 'history',
    base: '/app/',
    routes
});

Vue.use(ElementUI);
Vue.use(VueRouter);

new Vue({
    router,
    el: '#app',
    render: h => h(App)
})