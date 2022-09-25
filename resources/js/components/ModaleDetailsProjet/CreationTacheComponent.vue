<template>
  <div class="toto">
    <div
      class="modale-overlay-tache"
      v-show="visibilite[index]"
      @click="visibilite[index] = false"
    ></div>

    <div v-show="!visibilite[index]">
      <button @click="showVisibilite()">créer une tâche</button>
    </div>
    <div class="modalCreationTache" v-show="visibilite[index]">
      <form @submit.prevent="creationTache()">
        <input type="text" v-model="titreTache" />
        <button>valider</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: Number,
    index: Number,
    watchIndex: Number,
  },
  watch: {
    // whenever question changes, this function will run
    watchIndex(e) {
      if (e == this.index) {
        this.visibilite[this.index] = false;
      }
      return this.$emit("watchNull", null);
    },
  },

  data() {
    return {
      titreTache: "",
      visibilite: [],
    };
  },
  methods: {
    async creationTache() {
      const res = await axios.post(`api/taches/${this.id}`, {
        titreTache: this.titreTache,
      });
      if (res.status === 200) {
        console.log(res, "component Res");
      }
    },
    showVisibilite() {
      console.log(this.index, "showdzada");
      this.$emit("envoieIndex", this.index);
      this.visibilite[this.index] = true;
      this.$emit("modaleTache", this.visibilite[this.index]);
    },
  },
};
</script>

<style scoped>
.modalCreationTache {
  position: relative;
  z-index: 99;
  display: flex;
  width: 9rem;
}
.modalCreationTache input {
  padding: 0px;
  width: 98%;
  border: none;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  background-color: #d3edf8;
  margin-bottom: 20px;
  height: 1.2rem;
  padding-top: 3px;
  padding-bottom: 3px;
  padding-right: 1%;
  padding-left: 1%;
}
button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.6em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
  text-transform: uppercase;
  width: 9rem;
}
</style>