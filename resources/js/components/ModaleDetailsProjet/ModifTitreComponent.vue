<template>
  <div class="container">
    <input type="text" v-model="titre" />
    <button @click="modificationTitre">valider</button>
  </div>
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

<style scoped>
.container {
  display: flex;
  width: 100%;
  flex-direction: column;
}
.container input {
  margin-bottom: 6px;
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
}
.container button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.6em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
  width: 100%;
  text-transform: uppercase;
  margin-bottom: 8px;
}
</style>