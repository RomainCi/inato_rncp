import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../../views/HomeView.vue";
import EditView from "../../views/EditView.vue";
import ProjetView from "../../views/ProjetView.vue";
import NotificationView from "../../views/NotificationView.vue";
import ListesView from "../../views/ListesView.vue";
import axios from "axios";

const routes = [
    {
        path: "/",
        name: "home",
        component: HomeView,
    },
    {
        path: "/edit",
        name: "edit",
        component: EditView,
    },
    {
        path: "/projet",
        name: "projet",
        component: ProjetView,
        meta: { requiresAuth: true },
    },
    {
        path: "/notification",
        name: "notification",
        component: NotificationView,
    },
    {
        path: "/detailsProjet",
        name: "detailsProjet",
        component: ListesView,
        props: true,
    },
];
const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !localStorage.getItem("authAcces")) {
        return next({ name: "home" });
    } else {
        return next();
    }
});

export default router;
