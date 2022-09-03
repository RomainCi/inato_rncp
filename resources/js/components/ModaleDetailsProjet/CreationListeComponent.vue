<template>
  <div>
    <div v-show="!show">
      <p @click="show = !show">création de liste +</p>
    </div>
    <div v-show="show">
      <form @submit.prevent="creationListe">
        <input type="text" v-model="titre" />
        <button>valider</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: false,
      titre: "",
    };
  },
  methods: {
    async creationListe() {
      let id = localStorage.getItem("idProjet");
      const res = await axios.post(`api/listes/${id}`, {
        titreListe: this.titre,
      });
      if (res.status === 200) {
        this.titre = "";
        this.show = !this.show;
      }
    },
  },
};
</script>

<style scoped>
</style>