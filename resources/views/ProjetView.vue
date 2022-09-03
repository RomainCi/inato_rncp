<template>
  <div>
    <navbar></navbar>
    <h1>Projet</h1>
    <projet></projet>
  </div>
</template>

<script>
import NavbarComponent from "../js/components/NavbarComponent.vue";
import ProjetComponent from "../js/components/ProjetComponent.vue";
const ProjetView = {
  components: {
    projet: ProjetComponent,
    navbar: NavbarComponent,
  },
  data() {
    return {};
  },
  methods: {},
  // mounted() {
  //   if (localStorage.getItem("reloaded")) {
  //     // The page was just reloaded. Clear the value from local storage
  //     // so that it will reload the next time this page is visited.
  //     localStorage.removeItem("reloaded");
  //   } else {
  //     // Set a flag so that we know not to reload the page twice.
  //     localStorage.setItem("reloaded", "1");
  //     location.reload();
  //   }
  // },
};
export default ProjetView;
</script>

<style>
</style>