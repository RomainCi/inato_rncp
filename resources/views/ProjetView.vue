<template>
  <div class="mainContainer">
    <div class="container">
      <div>
        <navbar :floutage="floutage"></navbar>

        <projet
          v-on:floutage="ajoutFloutage($event)"
          v-on:noFloutage="enleveFloutage($event)"
        ></projet>
      </div>
      <div :class="floutage" class="containerImg">
        <p>PROJETS</p>
      </div>
    </div>
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
    return {
      floutage: "",
    };
  },
  methods: {
    ajoutFloutage(e) {
      console.log(e, "floutage");
      this.floutage = e;
    },
    enleveFloutage(e) {
      this.floutage = e;
    },
  },

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

<style scoped>
.floutage {
  filter: blur(8px);
}
.mainContainer {
  /* background: red; */
  background: linear-gradient(
    90deg,
    rgba(13, 61, 174, 1) 35%,
    rgba(15, 192, 180, 1) 100%
  );
  min-height: 100vh;
  /* width: 100vw; */
  /* padding-top: 2vh; */
  display: flex;
  justify-content: center;
}
.mainContainer .container {
  align-self: center;
  min-height: 90vh;
  width: 94vw;
  background-color: #0d85bc;
  border-radius: 30px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.containerImg {
  align-self: flex-end;
  background-image: url("../js/assets/projet.png");
  background-size: cover;
  height: 200px;
  width: 200px;
  display: flex;
  justify-content: flex-end;
  align-items: flex-end;
  margin-right: 5%;
  margin-bottom: 5%;
}
.containerImg p {
  color: white;
  font-family: "Lexend Mega", sans-serif;
  font-size: 1.2em;
  letter-spacing: -3px;
  margin-right: 10%;
  margin-bottom: 26%;
}
@media only screen and (min-width: 700px) {
  .mainContainer .container {
    border-radius: 50px;
  }
}
@media only screen and (min-width: 1200px) {
  .containerImg {
    margin: 0px;
  }
}
</style>
