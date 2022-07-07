import Vue from 'vue';
import VueRouter from "vue-router";

Vue.use(VueRouter)

import currencies from "../pages/currencies";
import currencyCRUD from "../pages/currencyCRUD";
import register from "../pages/register";
import login from "../pages/login";
import currencyRateCRUD from "../pages/currencyRateCRUD";

const routes = [
    {
        component: currencies,
        name: "currencies",
        path:"/"
    },
    {
        component: currencyCRUD,
        name: "currencyCRUD",
        path:"/currencyCRUD"
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
    {
        component: currencyRateCRUD,
        name: "currencyRateCRUD",
        path:"/currencyRateCRUD"
    },
];

export default new VueRouter({
    routes
});