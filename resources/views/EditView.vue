<template>
  <div class="mainContainer">
    <div class="container">
      <div>
        <navbar />
        <edit></edit>
      </div>

      <div class="containerImage">
        <p>compte</p>
        <img src="../js/assets/edit.png" alt="edit" />
      </div>
    </div>
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

<style scoped>
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
.containerImage {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-left: 5%;
  margin-right: 5%;
}
.containerImage p {
  color: white;
  font-family: "Lexend Mega", sans-serif;
  font-size: 1.2em;
  text-transform: uppercase;
}
.containerImage img {
  height: 210px;
  width: 210px;
}
@media only screen and (min-width: 1100px) {
  .containerImage {
    display: none;
  }
}
</style>