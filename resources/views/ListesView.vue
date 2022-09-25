<template>
  <div>
    <div class="mainContainer">
      <div class="container">
        <Navbar :floutage="floutage"></Navbar>
        <div>
          <listes
            :listes="responseListes"
            :role="responseRole"
            :nomProjet="responseNom"
            v-on:changeRole="updateRole($event)"
            v-on:floutage="floutageAE($event)"
          ></listes>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavbarComponent from "../js/components/NavbarComponent.vue";
import ListesComponent from "../js/components/DetailsProjet/ListesComponent.vue";
import router from "../js/router";
export default {
  components: {
    Navbar: NavbarComponent,
    listes: ListesComponent,
  },

  data() {
    return {
      responseListes: "",
      responseRole: "",
      responseNom: "",
      floutage: "",
    };
  },
  beforeMount() {
    this.detailsProjet();
  },
  methods: {
    updateRole(event) {
      if (event == 0) {
        console.log("jes suis dans le 00000000");
        router.push({ name: "projet" });
      } else {
        this.responseRole = event.role;
      }
    },
    async detailsProjet() {
      let id = localStorage.getItem("idProjet");
      const res = await axios.get(`api/listes/${id}`);
      console.log(res);
      this.responseListes = res.data.listes;
      console.log(typeof this.responseListes);
      this.responseRole = res.data.role;
      this.responseNom = res.data.listes[0].list_projet.nom;
      console.log(this.responseRole, "response Role");
    },
    floutageAE(e) {
      console.log("je suis le ", e);
      this.floutage = e;
    },
  },
};
</script>

<style scoped>
.floutage {
  filter: blur(8px);
}
.mainContainer {
  background: linear-gradient(
    90deg,
    rgba(13, 61, 174, 1) 35%,
    rgba(15, 192, 180, 1) 100%
  );
  min-height: 98vh;
  width: 100vw;
  padding-top: 2vh;
  display: flex;
  justify-content: center;
}
.container {
  min-height: 90vh;
  width: 94vw;
  background-color: #0d85bc;
  border-radius: 30px;
}
</style>