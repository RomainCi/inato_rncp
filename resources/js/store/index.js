import { createStore } from "vuex";

export default createStore({
    state: {
        nombre: "",
    },
    getters: {},
    mutations: {
        notifNbr(state, nbr) {
            state.nombre = nbr;
        },
    },
    actions: {
        envoieNbr: ({ commit }, nbr) => {
            commit("notifNbr", nbr);
        },
    },
    modules: {},
});
