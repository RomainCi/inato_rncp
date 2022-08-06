<template>
  <div>
    <p>Vous devez écrire supprimer pour supprimer le projet</p>
    <form @submit.prevent="deleteProjet">
      <input type="text" v-model="supprimer" />
      <button>valider</button>
    </form>
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
          this.dataProjet();
          this.modaleProjet();
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

<style>
</style>