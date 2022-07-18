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
  mounted() {},
};
export default ProjetView;
</script>

<style>
</style>