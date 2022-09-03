import { createStore } from "vuex";

export default createStore({
    state: {
        id: "",
    },
    getters: {},
    mutations: {
        idN(state, ide) {
            state.id = ide;
        },
    },
    actions: {
        envoieId: ({ commit }, id) => {
            commit("idN", id);
        },
    },
    modules: {},
});
