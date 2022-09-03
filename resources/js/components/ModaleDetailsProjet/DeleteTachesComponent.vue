<template>
  <li @click="deleteTache">supprimer</li>
</template>

<script>
export default {
  props: {
    idContent: Number,
    content: String,
  },
  methods: {
    async deleteTache() {
      if (this.content === "tache") {
        const res = await axios.delete(`api/taches/${this.idContent}`);
        if (res.status === 200) {
          // console.log(res, "la res");
          // let liste = this.listes.find((e) => e.id === res.data.listes.id);
          // liste.list_taches = res.data.listes.list_taches;
          this.$emit("closePopup", false);
          // this.popup[this.idModale] = false;
        }
      } else {
        console.log(this.idContent, "id content");
        const res = await axios.delete(`api/listes/${this.idContent}`);
        if (res.status === 200) {
          // console.log(res, "la res");
          // let liste = this.listes.find((e) => e.id === res.data.listes.id);
          // liste.list_taches = res.data.listes.list_taches;
          this.$emit("closePopup", false);
          // this.popup[this.idModale] = false;
        }
      }
    },
  },
};
</script>

<style>
</style>