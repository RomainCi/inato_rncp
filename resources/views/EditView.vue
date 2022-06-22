<template>
  <div>
    <navbar />
    <h1>Compte</h1>
    <edit></edit>
  </div>
</template>

<script>
import EditComponent from "../js/components/EditComponent.vue";
import NavbarComponent from "../js/components/NavbarComponent.vue";
const Editview = {
  components: {
    edit: EditComponent,
    navbar: NavbarComponent,
  },
  data() {
    return {};
  },
  methods: {},
};
export default Editview;
</script>

<style>
</style>