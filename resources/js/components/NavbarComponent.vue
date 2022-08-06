<template>
  <div>
    <nav class="navbar">
      <input type="hidden" :value="nombreNotif" />
      <router-link to="/edit">mon compte</router-link>
      <router-link to="/projet">projet</router-link>
      <router-link to="/notification"
        ><i class="fa-solid fa-bell">{{ nbr }}</i></router-link
      >
    </nav>
  </div>
</template>

<script>
import axios from "axios";
import store from "../store";
export default {
  data() {
    return {
      nbr: "",
    };
  },
  beforeUpdate() {
    console.log(this.nombreNotif, "beforeMount");
    this.nbr = this.nbr - this.nombreNotif;
  },

  computed: {
    nombreNotif() {
      return store.state.nombre;
    },
  },

  beforeMount() {
    this.numberNotif();
    window.Echo.private("App.Models.User." + User.id).notification(
      (notification) => {
        console.log(notification, "hello");
        this.nbr = this.nbr + 1;
      }
    );
  },
  methods: {
    async numberNotif() {
      const res = await axios.get("api/notifications");
      console.log(res);
      this.nbr = res.data.nombreNotif;
    },
  },
};
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-around;
}
</style>