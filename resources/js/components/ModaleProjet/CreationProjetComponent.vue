<template>
  <div>
    <div class="modal-overlay" v-show="showModale" @click="closeModale"></div>

    <div :class="floutage" class="container" @click="actionModale">
      <img src="../../assets/logoPlus.png" alt="logoPlus" />
      <div class="containerSpan">
        <span>CRÉER UN </span> <span>NOUVEAU</span> <span> PROJET</span>
      </div>
    </div>

    <div class="modal" v-show="showModale">
      <form class="formulaire" @submit.prevent="submitForm">
        <label for="projet">NOM DU PROJET</label>
        <input type="text" v-model="titre" />
        <!-- 
        <label for="image">Votre fond écran</label>
        <input type="file" @change="file" accept="image/*" /> -->
        <div class="containerImg">
          <div v-for="(element, index) in publicUrl" :key="index">
            <img
              class="back"
              :src="`${element.url}`"
              alt=""
              @click="idBackground(element.id)"
            />
          </div>
        </div>
        <button>VALIDER</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const CreationProjetComponent = {
  props: {
    retire: String,
  },
  data() {
    return {
      image: null,
      showModale: false,
      publicUrl: "",
      titre: "",
      floutage: "noFLoutage",
    };
  },
  beforeMount() {
    this.datasPublicUrl();
  },
  methods: {
    async datasPublicUrl() {
      const res = await axios.get(`api/projet/publicUrl/backgroundImage`);
      console.log(res, "publicUrl");
      this.publicUrl = res.data.public;
    },
    async submitForm() {
      let fd = new FormData();
      console.log(this.image);
      if (this.image != null) {
        fd.append("image", this.image);
      }
      fd.append("titre", this.titre);
      await axios.get("sanctum/csrf-cookie");
      const res = await axios.post("api/projet", fd);
      console.log(res.data.projet, "creation projet projet");
      this.$emit("ajoutProjet", res.data.projet);
    },
    idBackground(id) {
      this.image = id;
    },
    file(e) {
      this.image = e.target.files[0];
    },
    actionModale() {
      this.showModale = true;
      this.floutage = "floutage";
      this.$emit("floutage", "floutage");
    },
    closeModale() {
      this.showModale = false;
      this.floutage = "noFLoutage";
      this.$emit("noFloutage", "noFloutage");
    },
  },
};
export default CreationProjetComponent;
</script>

<style scoped>
.floutage {
  filter: blur(8px);
}
.container {
  background-color: #0964d7;
  height: 8vh;
  margin-left: 5%;
  margin-right: 5%;
  border-radius: 10px;
  margin-top: 45px;
  display: flex;
  cursor: pointer;
  align-items: center;
}
.container .containerSpan {
  margin-left: 10px;
  display: flex;
  flex-direction: column;
  color: white;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.7rem;
}
/* .container .containerSpan span {
} */
.container img {
  height: 40px;
  width: 40px;
  margin-left: 4%;
}
.back {
  height: 60px;
  width: 60px;
  background-size: cover;
}

.formulaire {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
}

.modal-overlay {
  border-radius: 10px;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  /* background-color: rgba(0, 0, 0, 0.3); */
  height: 100%;
  cursor: pointer;
}

.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 99;
  filter: blur(0px) !important;
  width: 75vw;
  height: 30vh;
  max-width: 600px;
  max-height: 600px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}
.modal .formulaire {
  height: 100%;
}
.modal .formulaire label {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
  font-size: 0.9em;
}
.modal .formulaire input {
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
}
.modal .formulaire .containerImg {
  display: flex;
  margin-top: 20px;
  flex-wrap: wrap;
  gap: 10px;
  flex-basis: 60px;
}
.modal .formulaire button {
  /* margin-top: 100px; */
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
}
.containerImg img {
  height: 60px;
  width: 60px;
  border-radius: 40px;
  cursor: pointer;
}
@media only screen and (min-width: 700px) {
  .containerImg img {
    height: 90px;
    width: 90px;
    border-radius: 60px;
  }
}
@media only screen and (min-width: 1200px) {
  .container {
    margin-left: 15%;
    margin-right: 15%;
    height: 10vh;
  }
  .container .containerSpan {
    font-size: 1rem;
  }
  .container img {
    height: 60px;
    width: 60px;
  }
  .containerImg img {
    height: 90px;
    width: 90px;
    border-radius: 60px;
  }
}
</style>