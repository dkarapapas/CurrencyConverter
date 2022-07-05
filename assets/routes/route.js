import Vue from 'vue';
import VueRouter from "vue-router";

Vue.use(VueRouter)

import currencies from "../pages/currencies";
import dlt from "../pages/delete";
import register from "../pages/register";
import login from "../pages/login";

const routes = [
    {
        component: currencies,
        name: "currencies",
        path:"/"
    },
    {
        component: dlt,
        name: "delete",
        path:"/delete"
    },
    {
        component: register,
        name: "register",
        path:"/register"
    },
    {
        component: login,
        name: "login",
        path:"/login"
    },
];

export default new VueRouter({
    routes
});