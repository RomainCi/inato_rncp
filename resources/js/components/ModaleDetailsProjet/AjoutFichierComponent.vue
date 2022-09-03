<template>
  <!-- <div class="modal-overlay" v-show="!show" @click="show = false">
    <p>hadzad</p>
  </div> -->
  <li @click="show = !show">Ajout fichier</li>
  <div class="modal" v-show="!show">
    <div class="close" @click="show = !show">&#x2715;</div>
    <h2>Ajout de fichier</h2>
    <input
      type="file"
      ref="fileuplaod"
      @change="file"
      accept="*"
      class="input-file"
    />
    <button @click="envoieFichier">valider</button>
    <!-- <a :href="url" @click.prevent="downloadItem()">Download Text</a> -->
  </div>
</template>

<script>
export default {
  props: {
    tache_id: Number,
  },
  data() {
    return {
      show: true,
      fichier: "",
    };
  },
  methods: {
    downloadItem() {
      console.log(this.url, "url");
      // axios
      //   .get(this.url, { responseType: "blob" })
      //   .then((response) => {
      const blob = new Blob([this.url], { type: "application/txt" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      console.log(link.href);
      // link.download = label.download;
      link.click();
      URL.revokeObjectURL(link.href);

      // .catch(console.error);
    },
    file(e) {
      this.fichier = e.target.files[0];
    },
    async envoieFichier() {
      try {
        let fd = new FormData();
        console.log(this.fichier);
        if (this.fichier != null) {
          fd.append("fichier", this.fichier);
          fd.append("tache_id", this.tache_id);
        }

        let id = localStorage.getItem("idProjet");
        // const res = await axios.post(`api/taches/fichier/${id}`, fd);
        // console.log(res);
        const res = await axios.post(`api/fichier`, fd);
        // responseType: "arraybuffer",
        console.log(res, "res fichier");
        // console.log(res.headers.getAllResponseHeaders());
        // let blob = new Blob([res.data], { type: "image/png; charset=binary" });
        // let link = document.createElement("a");
        // link.href = window.URL.createObjectURL(blob);
        // link.download = "test.png";
        // link.click();
        // URL.revokeObjectURL(link.href);
      } catch (e) {
        console.log(e);
      }
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  /* background-color: rgba(0, 0, 0, 0.3); */
  background-color: yellow;
  height: 100%;
}
.modal {
  position: fixed;
  top: 218%;
  left: -60%;
  transform: translate(-50%, -50%);
  z-index: 99;

  width: 100%;
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}
</style>