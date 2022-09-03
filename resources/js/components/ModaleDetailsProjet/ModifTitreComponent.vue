<template>
  <input type="text" v-model="titre" />
  <button @click="modificationTitre">valider</button>
</template>

<script>
export default {
  props: {
    idContent: Number,
    titre: String,
    content: String,
  },
  data() {
    return {};
  },
  methods: {
    async modificationTitre() {
      if (this.content === "tache") {
        console.log(this.titre);
        let id = localStorage.getItem("idProjet");
        await axios.put(`api/taches/titre/${id}`, {
          idTache: this.idContent,
          titreTache: this.titre,
        });
      } else {
        let id = localStorage.getItem("idProjet");
        await axios.put(`api/listes/titre/${id}`, {
          idListe: this.idContent,
          titreListe: this.titre,
        });
      }
    },
  },
};
</script>

<style>
</style>