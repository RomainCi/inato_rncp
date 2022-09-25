<template>
  <div class="modale-overlay" v-show="show" @click="show = false"></div>
  <div :class="floutageCss" class="container">
    <img class="logo" src="../../../public/images/logo.png" />
    <nav class="navbar">
      <input type="hidden" :value="(floutageCss = floutage)" />
      <router-link class="projet" to="/projet">PROJETS</router-link>
      <div class="box">
        <router-link class="notif" to="/notification">{{ nbr }}</router-link>
        <div class="deconnexion">
          <img src="../assets/photoProfil.png" @click="show = true" />
          <ul v-show="show">
            <li @click="deconnexion">DECONNEXION</li>
            <li><router-link to="/edit">EDIT</router-link></li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    watchNumber: Number,
    floutage: String,
  },
  data() {
    return {
      show: false,
      nbr: "",
      floutageCss: this.floutage,
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
        window.Echo.leave(`projet17`);
        localStorage.clear();
        this.$router.push("/");
      }
    },
  },
};
</script>

<style scoped>
/* .mainContainer {
  background: linear-gradient(
    90deg,
    rgba(13, 61, 174, 1) 35%,
    rgba(15, 192, 180, 1) 100%
  );
} */
.floutage {
  filter: blur(8px);
}
a:link {
  text-decoration: none;
}

.container {
  display: flex;
  margin-right: 5%;
  margin-left: 5%;
  margin-top: 40px;
}

.modale-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  cursor: pointer;
}
.deconnexion {
  height: 40px;
  width: 40px;
  margin-right: 10px;
}
.deconnexion img {
  height: 100%;
  width: 100%;
}
.navbar {
  display: flex;
  justify-content: space-between;
  background-color: white;
  width: 100%;
  border-radius: 10px;
}
.navbar img {
  height: 40px;
  width: 40px;
  margin-left: 10px;
}
.navbar .box {
  display: flex;
}
.navbar .projet {
  font-family: "Lexend Mega", sans-serif;
  color: black;
  align-self: center;
  margin-left: 20px;
}
.navbar .notif {
  background-image: url("../assets/notif.png");
  height: 100%;
  width: 40px;
  background-size: cover;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-right: 10px;
  color: red;
  font-family: "Lexend Mega", sans-serif;
}
.logo {
  height: 40px;
  width: 40px;
  margin-right: 10px;
}
ul {
  margin: 0px;
}
li {
  z-index: 99;
  position: relative;
  right: 90px;
  list-style: none;
  margin: 0px;
  font-family: "Lexend Mega", sans-serif;
  color: white;
  font-size: 0.6rem;
  cursor: pointer;
  padding-top: 8px;
  top: -6px;
}
li:hover {
  color: red;
}
li a:hover {
  color: red;
}
li a {
  color: white;
}
</style>