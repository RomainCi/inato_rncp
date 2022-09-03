<template>
  <div>
    <navbar
      v-on:watchNumberNull="this.watchNumber = $event"
      :watchNumber="watchNumber"
    ></navbar>
    <h1>Notificaction</h1>
    <notification v-on:luNotif="this.watchNumber = $event"></notification>
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

<style>
</style>