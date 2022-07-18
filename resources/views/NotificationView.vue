<template>
  <div>
    <navbar></navbar>
    <h1>Notificaction</h1>
    <notification></notification>
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
    return {};
  },
};
export default NotificationView;
</script>

<style>
</style>