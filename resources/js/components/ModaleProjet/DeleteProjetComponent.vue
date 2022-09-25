<template>
  <div>
    <div class="mainContainer">
      <p>Vous devez écrire "supprimer" pour supprimer le projet</p>
      <form @submit.prevent="deleteProjet">
        <input type="text" v-model="supprimer" />
        <button>valider</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    idDelete: Number,
  },
  data() {
    return {
      id: this.idDelete,
      supprimer: "",
    };
  },
  methods: {
    async deleteProjet() {
      if (this.supprimer === "supprimer") {
        const res = await axios.delete(`api/projet/${this.id}`);
        console.log(res);
        if (res.status === 200) {
          // this.dataProjet();
          // this.modaleProjet();
          window.Echo.leave(`projet${this.id}`);
        }
      } else {
        console.log("erreur");
      }
      console.log("je suis dans le delete");
    },
  },
};
</script>

<style scoped>
.mainContainer {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.mainContainer p {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
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
</style>