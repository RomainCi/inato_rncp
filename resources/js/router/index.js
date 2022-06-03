import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../../views/HomeView.vue";
import PageView from "../../views/PageView.vue";
const routes = [
    {
        path: "/",
        name: "home",
        component: HomeView,
    },
    {
        path: "/page",
        name: "page",
        component: PageView,
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
