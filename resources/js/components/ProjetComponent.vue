<template>
  <div>
    <h2>Création de projet</h2>

    <p @click="showModale = true">Création d'un projet</p>

    <div
      class="modal-overlay"
      v-show="showModale"
      @click="showModale = false"
    ></div>

    <transition name="slide" appear>
      <div class="modal" v-show="showModale">
        <div class="close" @click="showModale = false">&#x2715;</div>
        <form class="formulaire" @submit.prevent="submitForm">
          <label for="projet">titre</label>
          <input type="text" v-model="titre" />

          <label for="image">Votre fond écran</label>
          <input type="file" @change="file" accept="image/*" />
          <div>
            <ul>
              <li class="back"></li>
              <li class="back2"></li>
            </ul>
          </div>
          <button>valider</button>
        </form>
      </div>
    </transition>
    <h2>Mes projets</h2>
    <div v-for="(element, index) in response" :key="index">
      <p>{{ element.nom }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import imageFond from "../assets/image.jpg";
const ProjetComponent = {
  props: {
    datas: Object,
  },
  data() {
    return {
      cssProps: {
        backgroundImage: `url(${require("../assets/image.jpg")})`,
      },
      titre: "",
      imageFond: imageFond,
      response: "",
      showModale: false,
      image: null,
    };
  },
  beforeMount() {
    this.dataProjet();
  },
  methods: {
    ////requete/////////////
    async dataProjet() {
      //   const csrf = await axios.get("sanctum/csrf-cookie");
      //   console.log("csrf", csrf);
      const res = await axios.get("api/projet");
      this.response = res.data.projet;
    },
    async submitForm() {
      let fd = new FormData();
      if (this.image != null) {
        fd.append("image", this.image);
      }

      fd.append("titre", this.titre);
      await axios.get("sanctum/csrf-cookie");
      const res = await axios.post("api/projet", fd);

      this.response = res.data;
      console.log(this.response);
    },
    //////////fin requete//////////
    toto() {
      console.log(this.image);
    },
    file(e) {
      this.image = e.target.files[0];
    },
  },
};
export default ProjetComponent;
</script>

<style lang="scss" scoped>
.back {
  height: 60px;
  width: 60px;
  background-size: cover;
}
.back2 {
  background-image: url("../assets/image2.jpg");
  height: 60px;
  width: 60px;
  background-size: cover;
}
.formulaire {
  display: flex;
  flex-direction: column;
}
.close {
  display: flex;
  justify-content: flex-end;
  content: "&#10006";
}

#app {
  position: relative;

  display: flex;
  justify-content: center;
  align-items: center;

  width: 100vw;
  min-height: 100vh;
  overflow-x: hidden;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
}

.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 99;

  width: 100%;
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}

// .fade-enter-active,
// .fade-leave-active {
//   transition: opacity 0.5s;
// }

// .fade-enter,
// .fade-leave-to {
//   opacity: 0;
// }

.slide-enter,
.slide-leave-to {
  transform: translateY(-50%) translateX(100vw);
}
</style>

