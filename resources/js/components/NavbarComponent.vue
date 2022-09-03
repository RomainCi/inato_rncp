<template>
  <div>
    <div class="modale-overlay" v-show="show" @click="show = false"></div>
    <nav class="navbar">
      <!-- <input type="hidden" :value="nombreNotif" /> -->

      <router-link to="/projet">projet</router-link>
      <router-link to="/notification"
        ><i class="fa-solid fa-bell">{{ nbr }}</i></router-link
      >
      <div class="deconnexion">
        <img src="../assets/image.jpg" @click="show = true" />
        <ul v-show="show">
          <li @click="deconnexion">deconnexion</li>
          <li><router-link to="/edit">mon compte</router-link></li>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    watchNumber: Number,
  },
  data() {
    return {
      show: false,
      nbr: "",
    };
  },
  watch: {
    // whenever question changes, this function will run
    watchNumber(e) {
      console.log("eeeeNav", e);
      this.nbr = this.nbr - e;
      return this.$emit("watchNumberNull", null);
    },
  },

  beforeMount() {
    this.numberNotif();
    console.log(User.id, "user IDDDDDDD");
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
      console.log(res, "numberNotif");
      this.nbr = res.data.nombreNotif;
    },
    async deconnexion() {
      const res = await axios.post("api/users/deconnexion");
      console.log(res);
      if (res.status === 204) {
        localStorage.clear();
        this.$router.push("/");
      }
    },
  },
};
</script>

<style scoped>
li {
  z-index: 99;
  position: relative;
}
.modale-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
}
.deconnexion {
  height: 40px;
  width: 40px;
  /* background-image: url("../assets/image.jpg");
  background-size: cover; */
}
.deconnexion img {
  height: 100%;
  width: 100%;
}
.navbar {
  display: flex;
  justify-content: space-around;
}
</style>