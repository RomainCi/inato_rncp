<template>
  <div>
    <div v-show="!show">
      <p @click="show = !show">création de liste +</p>
    </div>
    <div v-show="show">
      <form @submit.prevent="creationListe">
        <input type="text" v-model="titre" />
        <button>valider</button>
      </form>
    </div>
    <!-- Modale -->
    <div
      class="modal-overlay"
      v-show="editModale"
      @click="editModale = false"
    ></div>
    <div
      class="modal-overlay-bis"
      v-show="popup[this.idModale]"
      @click="popup[this.idModale] = false"
    ></div>
    <div v-if="editModale" class="modal">
      <EditTaches></EditTaches>
    </div>
    <!-- fin Modale -->
    <div class="bigContainer">
      <div class="container" v-for="(element, indexo) in listes" :key="indexo">
        <div v-show="!visibilite[indexo]">
          <p @click="showVisibilite(indexo)">création de tache +</p>
        </div>
        <div v-show="visibilite[indexo]">
          <form @submit.prevent="creationTache(indexo, element.id)">
            <input type="text" v-model="titreTache" />
            <button>valider</button>
          </form>
        </div>
        <ul
          class="miniContainer"
          @drop="onDrop($event, element.id)"
          @dragenter.prevent
          @dragover.prevent
        >
          {{
            element.titre_list
          }}

          <li
            v-for="(e, inde) in element.list_taches"
            :key="inde"
            @dragstart="startDrag($event, e)"
            draggable="true"
            class="livisi"
            :value="e.index"
          >
            {{ e.titre_tache
            }}<i
              class="fa-solid fa-ellipsis-vertical"
              @click="showOption(e.id, e.titre_tache)"
            ></i>
            <div class="modalBiss" v-show="popup[e.id]">
              <input type="text" v-model="titreTache" />
              <button @click="modificationTitre">valider</button>
            </div>
            <div class="modalBis" v-show="popup[e.id]">
              <ul>
                <li @click="deleteTache">supprimer</li>
                <li @click="modaleInvitation()">inviter</li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import DeleteTachesComponent from "../ModaleDetailsProjet/DeleteTachesComponent.vue";
export default {
  components: {
    DeleteTaches: DeleteTachesComponent,
  },
  props: {
    listes: Object,
  },
  data() {
    return {
      show: false,
      titre: "",
      visibilite: [],
      titreTache: "",
      lastIndexo: "",
      editModale: false,
      popup: [],
      idModale: "",
      titreTache: "",
    };
  },
  beforeMount() {
    // this.dataTaches();
  },
  methods: {
    startDrag(event, item) {
      console.log(item, "item");
      event.dataTransfer.dropEffect = "move";
      event.dataTransfer.effectAllowed = "move";
      event.dataTransfer.setData("itemId", item.id.toString());
      event.dataTransfer.setData("listId", item.list_id.toString());
      event.dataTransfer.setData("lastIndex", item.index.toString());
    },
    onDrop(event, categoryId) {
      const itemId = parseInt(event.dataTransfer.getData("itemId"));
      const listId = parseInt(event.dataTransfer.getData("listId"));
      const lastIndex = parseInt(event.dataTransfer.getData("lastIndex"));
      let indexa = event.target._value;
      indexa = indexa + 1;
      console.log(this.listes, "toute les listes");
      if (event.target.localName == "li") {
        this.modifPosition(itemId, indexa, categoryId, lastIndex, listId);
      } else {
        indexa = 0;
        this.modifPosition(itemId, indexa, categoryId, lastIndex, listId);
      }
    },
    // endDrag(event, id) {
    //   event.target.style.opacity = 1;
    //   console.log("event end ", event);
    //   console.log("index de la colone de prise", id);
    // },
    // async dataTaches() {
    //   let id = localStorage.getItem("idProjet");
    //   const res = await axios.get(`api/taches/${id}`);
    //   console.log(res);
    // },
    async deleteTache() {
      console.log(this.idModale);
      const res = await axios.delete(`api/taches/${this.idModale}`);
      console.log(res);
    },
    async modificationTitre() {
      console.log(this.idModale);
      console.log(this.titreTache);
      let id = localStorage.getItem("idProjet");
      const res = await axios.put(`api/taches/titre/${id}`, {
        idTache: this.idModale,
        titreTache: this.titreTache,
      });
      if (res.status == 200) {
        const liste = this.listes.find((e) => (e.id = res.data.tache.list_id));
        let listeTache = liste.list_taches.find(
          (e) => (e.id = res.data.tache.id)
        );
        console.log("hellowE", listeTache);
        listeTache.titre_tache = res.data.tache.titre_tache;
        console.log(res.data.tache);
        console.log(this.listes);
      }
      console.log(res);
    },

    async modifPosition(tacheId, index, listeId, lastIndex, lastListeId) {
      let id = localStorage.getItem("idProjet");
      const res = await axios.put(`api/taches/position/${id}`, {
        tacheId: tacheId,
        index: index,
        listeId: listeId,
        lastIndex: lastIndex,
        lastListeId: lastListeId,
      });
      if (res.status == 200) {
        const lastListess = this.listes.find(
          (e) => e.id === res.data.lastListes.id
        );
        lastListess.list_taches = res.data.lastListes.list_taches;
        const listess = this.listes.find((e) => e.id === res.data.listes.id);
        listess.list_taches = res.data.listes.list_taches;
      }
    },
    async creationListe() {
      let id = localStorage.getItem("idProjet");
      const res = await axios.post(`api/listes/${id}`, {
        titreListe: this.titre,
      });
      if (res.status === 200) {
        console.log(res);
        this.show = !this.show;
        console.log(this.listes, "response");
        this.listes.push(res.data.liste);
      }
    },
    showVisibilite(index) {
      console.log(index);
      console.log(this.lastIndexo, "lastIndex");
      this.visibilite[this.lastIndexo] = false;
      this.lastIndex = index;
      this.visibilite[index] = true;
    },
    async creationTache(index, id) {
      console.log(this.titreTache, id);
      const res = await axios.post(`api/taches/${id}`, {
        titreTache: this.titreTache,
      });
      if (res.status === 200) {
        console.log(res, "taches store");
        let liste = this.listes.find((e) => e.id === res.data.tache.list_id);
        liste.list_taches.push(res.data.tache);
      }
    },
    showOption(id, titreTache) {
      this.titreTache = titreTache;
      console.log(id);
      this.idModale = id;
      this.popup[id] = true;
      // console.log(index);
      // this.editModale = true;
    },
  },
};
</script>

<style scoped>
.bigContainer {
  display: flex;
  position: absolute;
}
.livisi {
  background-color: yellow;
  height: 30px;
  position: relative;
}

.miniContainer {
  background-color: green;
  margin-left: 2px;
  margin-right: 2px;
}
.drop-zone {
  background-color: yellow;
  /* margin-bottom: 10px; */
  padding: 10px;
  height: 30px;
  /* height: 100px; */
}
.test {
  height: 30px;
  background-color: red;
}
.containerOnDrop {
  background-color: green;
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
.modal-overlay-bis {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
  height: 100%;
}
.modalBis {
  transform: translate(-50%, -50%);
  top: -85%;
  left: 152%;
  z-index: 99;
  margin: 0px;
  background-color: red;
  position: relative;
}
.modalBiss {
  display: flex;
  position: relative;
  z-index: 99;
  top: -20px;
}
.modalBiss input {
  width: 50%;
}
</style>