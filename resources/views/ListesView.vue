<template>
  <div>
    <Navbar></Navbar>

    <h1>Hello</h1>
    <listes :listes="responseListes"></listes>
  </div>
</template>

<script>
import axios from "axios";
import NavbarComponent from "../js/components/NavbarComponent.vue";
import ListesComponent from "../js/components/DetailsProjet/ListesComponent.vue";
export default {
  components: {
    Navbar: NavbarComponent,
    listes: ListesComponent,
  },

  data() {
    return {
      responseListes: "",
    };
  },
  beforeMount() {
    this.detailsProjet();
  },
  methods: {
    async detailsProjet() {
      let id = localStorage.getItem("idProjet");
      const res = await axios.get(`api/listes/${id}`);
      console.log(res);
      this.responseListes = res.data.listes;
      console.log(typeof this.responseListes);
    },
  },
};
</script>

<style>
</style>