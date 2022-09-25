require("./bootstrap");

import { createApp } from "vue";

import router from "./router";

import App from "./App.vue";
import store from "./store";
import Select2 from "vue3-select2-component";

createApp(App)
    .use(router)
    .use(store)
    .component("Select2", Select2)
    .mount("#app");
