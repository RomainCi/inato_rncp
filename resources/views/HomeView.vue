<template>
  <section class="home">
    <header>
      <!-- logo -->

      <h1>Inato</h1>
    </header>
    <!-- component -->
    <div class="container">
      <connexion titre="connexion" :inscription="show" v-show="visible" />
      <inscription titre="inscription" :connexion="show" v-show="!visible" />
    </div>
    <div>
      <router-link to="/page">page</router-link>
    </div>
  </section>
</template>

<script>
// @ is an alias to /src
import ConnexionComponent from "../js/components/ConnexionComponent.vue";
import InscriptionComponent from "../js/components/InscriptionComponent.vue";

const HomeView = {
  components: {
    connexion: ConnexionComponent,
    inscription: InscriptionComponent,
  },
  data() {
    return {
      visible: true,
    };
  },
  methods: {
    show() {
      return (this.visible = !this.visible);
    },
  },
};
export default HomeView;
</script>

<style scoped>
.home {
  height: 100vh;
}
.home img {
  height: 80px;
}
.container {
  display: flex;
  justify-content: center;
}
</style>