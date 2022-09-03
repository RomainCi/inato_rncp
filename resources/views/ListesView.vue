<template>
  <div>
    <Navbar></Navbar>

    <h1>Hello</h1>
    <listes
      :listes="responseListes"
      :role="responseRole"
      v-on:changeRole="updateRole($event)"
    ></listes>
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
      console.log(this.responseRole);
    },
  },
};
</script>

<style>
</style>