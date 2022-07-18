require("./bootstrap");

import { createApp } from "vue";

import router from "./router";

import App from "./App.vue";
import store from "./store";
import axios from "axios";
import Select2 from "vue3-select2-component";
axios.defaults.withCredentials = true;
axios.defaults.baseURL = "http://127.0.0.1:8000";

createApp(App)
    .use(router)
    .use(store)
    .component("Select2", Select2)
    .mount("#app");
