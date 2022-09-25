<template>
  <div class="mainContainer">
    <div class="container">
      <div>
        <navbar
          v-on:watchNumberNull="this.watchNumber = $event"
          :watchNumber="watchNumber"
        ></navbar>
        <notification v-on:luNotif="this.watchNumber = $event"></notification>
      </div>
      <div class="containerImage">
        <p>notifications</p>
        <img src="../js/assets/notification.png" alt="" />
      </div>
    </div>
  </div>
</template>

<script>
import NavbarComponentVue from "../js/components/NavbarComponent.vue";
import NotificationComponent from "../js/components/NotificationComponent.vue";
const NotificationView = {
  components: {
    navbar: NavbarComponentVue,
    notification: NotificationComponent,
  },
  data() {
    return {
      watchNumber: null,
      nbr: "",
    };
  },
  methods: {
    toto(e) {
      console.log(e, "ttotootot testststs");
    },
  },
};
export default NotificationView;
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
  margin-left: 5%;
}
.containerImage img {
  height: 210px;
  width: 210px;
}
.containerImage p {
  color: white;
  font-family: "Lexend Mega", sans-serif;
  font-size: 1.2em;
  text-transform: uppercase;
  position: relative;
  top: 185px;
  left: 65px;
  width: 0px;
}
@media only screen and (min-width: 1100px) {
  .containerImage {
    display: none;
  }
}
</style>