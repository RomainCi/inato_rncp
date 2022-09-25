<template>
  <li @click="recupFichier">download</li>
  <div class="modal" v-show="!show">
    <div class="close" @click="show = !show">&#x2715;</div>
    <p>download</p>
    <div v-if="response != null">
      <div v-for="(element, index) in response" :key="index">
        <p class="download" @click="downloadFichier(element.id)">
          {{ element.nom }}
        </p>
      </div>
    </div>
    <div v-else>
      <p>Aucun fichier a télecharger</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  props: {
    tache_id: Number,
  },
  beforeMount() {
    // this.recupFichier();
  },
  data() {
    return {
      show: true,
      response: "",
      nom: "",
    };
  },
  methods: {
    async recupFichier() {
      this.show = !this.show;
      console.log(this.tache_id);
      const res = await axios.get(`api/fichier/${this.tache_id}`);
      if (res.status === 200) {
        if (res.data.fichier.length === 0) {
          this.response = null;
        } else {
          this.response = res.data.fichier;
        }
      }
    },
    async downloadFichier(id) {
      console.log(id);
      const res = await axios.get(`api/fichier/downloadFichier/${id}`, {
        responseType: "arraybuffer",
      });
      console.log(res);
      let blob = new Blob([res.data], {
        type: res.headers.responsecontenttype,
      });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = res.headers.responsenomtype;
      link.click();
      URL.revokeObjectURL(link.href);
    },
    test() {
      let blob = new Blob([this.response.data], {
        type: this.response.headers.responsecontenttype,
      });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = this.nom;
      link.click();
      URL.revokeObjectURL(link.href);
    },
  },
};
</script>

<style scoped>
.close {
  cursor: pointer;
  width: 5%;
}
.download {
  cursor: pointer;
}
.download:hover {
  color: #1ea3dc;
}
li {
  /* color: black;
  font-size: 0.6rem;
  font-family: "Lexend Mega", sans-serif;
  text-transform: uppercase;
  cursor: pointer;
  text-align: center; */
  cursor: pointer;
  text-align: center;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.6em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
  text-transform: uppercase;
  margin-bottom: 6px;
}
/* li:hover {
  color: red;
} */
p {
  color: black;
  font-size: 0.8rem;
  font-family: "Lexend Mega", sans-serif;
  text-transform: uppercase;
  text-align: center;
}
.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 99;
  min-height: 120px;
  width: 75vw;
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}
</style>