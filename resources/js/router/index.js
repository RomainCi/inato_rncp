import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../../views/HomeView.vue";
import EditView from "../../views/EditView.vue";
import ProjetView from "../../views/ProjetView.vue";
import NotificationView from "../../views/NotificationView.vue";
import ListesView from "../../views/ListesView.vue";

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

export default createRouter({
    history: createWebHistory(),
    routes,
});
