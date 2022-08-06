<template>
  <div>
    <div
      class="modal-overlay"
      v-show="showModale"
      @click="showModale = false"
    ></div>

    <h2>Création de projet</h2>

    <div>
      <span @click="showModale = true">Création d'un projet</span>
    </div>

    <div class="modal" v-show="showModale">
      <div class="close" @click="showModale = false">&#x2715;</div>

      <form class="formulaire" @submit.prevent="submitForm">
        <label for="projet">titre</label>
        <input type="text" v-model="titre" />

        <label for="image">Votre fond écran</label>
        <input type="file" @change="file" accept="image/*" />

        <div v-for="(element, index) in publicUrl" :key="index">
          <img
            class="back"
            :src="`${element.url}`"
            alt=""
            @click="idBackground(element.id)"
          />
        </div>

        <button>valider</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const CreationProjetComponent = {
  props: {},
  data() {
    return {
      image: null,
      showModale: false,
      publicUrl: "",
      titre: "",
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
      console.log(res, "delete projet");
    },
    idBackground(id) {
      this.image = id;
    },
    file(e) {
      this.image = e.target.files[0];
    },
  },
};
export default CreationProjetComponent;
</script>

<style lang="scss" scoped>
.back {
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

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
  height: 100%;
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
</style>