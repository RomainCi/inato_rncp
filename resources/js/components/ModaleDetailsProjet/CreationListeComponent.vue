<template>
  <div>
    <div class="mainContainer">
      <p>créer une liste</p>
      <form @submit.prevent="creationListe">
        <label for="">TITRE</label>
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
.mainContainer {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  filter: blur(0px) !important;
}
.mainContainer p {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-transform: uppercase;
}
.mainContainer form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-around;
  width: 100%;
  gap: 15px;
}
.mainContainer form input {
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
  width: 100%;
}
.mainContainer form button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
}
.mainContainer form label {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
  font-size: 0.9em;
}
@media only screen and (min-width: 1200px) {
  /* .mainContainer {
    margin-left: 15%;
    margin-right: 15%;
    height: 10vh;
  } */
}
</style>