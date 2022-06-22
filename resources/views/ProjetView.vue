<template>
  <div>
    <navbar></navbar>
    <h1>Projet</h1>
    <projet :datas="response"></projet>
  </div>
</template>

<script>
import axios from "axios";
import NavbarComponent from "../js/components/NavbarComponent.vue";
import ProjetComponent from "../js/components/ProjetComponent.vue";
const ProjetView = {
  components: {
    projet: ProjetComponent,
    navbar: NavbarComponent,
  },
  data() {
    return {
      response: "",
    };
  },
  methods: {
    async projet() {
      const csrf = await axios.get("sanctum/csrf-cookie");
      console.log("csrf", csrf);
      const res = await axios.get("api/projet");
      this.response = res.data.projet;
      console.log(this.response);
    },
  },
  mounted() {
    // this.projet();
  },
};
export default ProjetView;
</script>

<style>
</style>