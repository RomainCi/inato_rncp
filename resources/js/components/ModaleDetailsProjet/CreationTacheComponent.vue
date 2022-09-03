<template>
  <div>
    <div
      class="modale-overlay-tache"
      v-show="visibilite[index]"
      @click="visibilite[index] = false"
    ></div>

    <div v-show="!visibilite[index]">
      <p @click="showVisibilite()">création de tache +</p>
    </div>
    <div class="modalCreationTache" v-show="visibilite[index]">
      <!-- <input type="hidden" :value="(index = tota)" /> -->
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
}
</style>