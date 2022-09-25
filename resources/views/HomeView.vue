<template>
  <section class="home">
    <div class="container">
      <img src="../../public/images/logo.png" />
      <connexion titre="connexion" :inscription="show" v-show="visible" />
      <inscription titre="inscription" :connexion="show" v-show="!visible" />
    </div>
  </section>
</template>

<script>
// @ is an alias to /src
import ConnexionComponent from "../js/components/ConnexionComponent.vue";
import InscriptionComponent from "../js/components/InscriptionComponent.vue";
// import logo from "../../public/images/logo.png";
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
  /* background: linear-gradient(#0d3dae, #cdcdcd); */
  background: linear-gradient(
    90deg,
    rgba(13, 61, 174, 1) 35%,
    rgba(15, 192, 180, 1) 100%
  );
}
.home img {
  height: 52vh;
}
.container {
  width: 80%;
  height: 90%;
  display: flex;
  justify-content: space-around;
  align-items: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #0d85bc;
  border-radius: 50px;
}
.container h1 {
  margin: 0px;
}
/* span {
  margin-right: 33%;
} */
</style>