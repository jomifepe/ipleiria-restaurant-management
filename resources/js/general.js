/*jshint esversion: 6 */

"use strict";

require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue';
import Vuex from 'vuex';
import store from './stores/global-store';
import VueRouter from 'vue-router';

import MenuList from './components/MenuComponent.vue';
import Login from './components/Login.vue';
import Logout from './components/Logout';
import Navigation from './components/sidebar/Navigation.vue';
import Register from './components/RegisterUser.vue';
import AlertMessage from './components/AlertMessage.vue';
import Profile from './components/Profile.vue';

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(store);

Vue.component('alert-message', AlertMessage);
Vue.component('menu-component', MenuList);
Vue.component('login-form', Login);
Vue.component('logout', Logout);
Vue.component('navigation', Navigation);
Vue.component('register', Register);

axios.defaults.baseURL = 'http://project.dad/api';

const routes = [
    { path: '/', component: MenuList, name: 'menu'},
    { path: 'profile', component: Profile, name: 'profile' },
    { path: 'login', component: Login, name: 'login'},
];

const router = new VueRouter({
    routes
});

/*
router.beforeEach((to, from, next) => {
    if ((to.name == 'profile') || (to.name == 'logout')) {
        if (!store.state.user) {
            next("/login");
            return;
        }
    }
    next();
});*/

const app = new Vue({
    data: {
        isUserLoggedIn: false,
        showMessage: false,
        showLoginForm: false,
        alertClass: "alert-success",
        alertMessage: "",
        showLogoutButton: false,
        showRegisterForm: false,
        loggedUser: null,
    },
    store,
    router,
    methods: {
        toggleLoginForm() {
            this.showLoginForm = !this.showLoginForm;
            this.showRegisterForm = false;
        },
        onLoginSuccessful(message) {
            this.isUserLoggedIn = true;
            this.showMessage = true;
            this.showRegisterForm = false;
            this.showLoginForm = false;
            this.alertClass = "alert-success";
            this.alertMessage = message;
            this.closeAlertMessage();
        },
        onLoginFailed(message) {
            this.showMessage = true;
            this.alertClass = "alert-danger";
            this.alertMessage = message;
        },
        onLogoutSuccessful() {
            this.isUserLoggedIn = false;
            this.showMessage = true;
            this.alertClass = "alert-success";
            this.alertMessage = "User was logged out successfully";
            this.closeAlertMessage();
            this.$router.go('/');
        },
        onLogoutFailed(){
            this.showMessage = true;
            this.alertClass = "alert-danger";
            this.alertMessage = "User logout out failed";
        },
        hasUserLoggedIn(){
          return this.$store.state.user != null;
        },
        toggleRegisterForm(){
            this.showRegisterForm = !this.showRegisterForm;
            this.showLoginForm = false;
        },
        onHideRegisterForm(){
            this.showRegisterForm = false;
        },
        closeAlertMessage() {
            setTimeout(() => {
                    this.showMessage = false
            }, 4000);
        },
        checkLogin() {
            let user = this.$store.state.user;
            if (user != null) {
                this.isUserLoggedIn = true;
            }
        },
        onCloseAlertMessage(){
            this.showMessage = false;
        },
        onRegisterSuccessful(message) {
            this.showRegisterForm = false;
            this.showMessage = true;
            this.alertClass = "alert-success";
            this.alertMessage = message;
            this.closeAlertMessage();
        },
        onRegisterFailed(message) {
            this.showMessage = true;
            this.alertClass = "alert-danger";
            this.alertMessage = message;
            this.closeAlertMessage();
        }
    },
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
        this.checkLogin();
    }
}).$mount('#app');
