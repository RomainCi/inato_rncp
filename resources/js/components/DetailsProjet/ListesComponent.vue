<template>
  <div>
    <div
      class="modal-overlay"
      v-show="showCreationListe"
      @click="showCreationListeModale"
    ></div>
    <div
      class="modal-overlay"
      v-show="editModale"
      @click="editModale = false"
    ></div>
    <div :class="floutage" class="containerCreation">
      <div class="containerRoleNom">
        <div>{{ nomProjet }}</div>
        <div>{{ role }}</div>
      </div>
      <div v-if="role != 'visiteur'">
        <button class="buttonListe" @click="showCreationListeFloutage">
          créer une liste
        </button>
      </div>
    </div>
    <div
      v-show="showCreationListe"
      class="containerListe modale"
      v-if="role != 'visiteur'"
    >
      <CreationListe></CreationListe>
    </div>
    <!-- Modale -->

    <div
      class="modale-overlay-tache"
      v-show="visibilite[this.indexModale]"
      @click="closeModale"
    ></div>
    <div
      class="modal-overlay-bis"
      v-show="popup[this.idModale]"
      @click="popup[this.idModale] = false"
    ></div>
    <div
      class="modal-overlay-bis"
      v-show="showModale[this.idModaleListe]"
      @click="showModaleListe()"
    ></div>
    <div v-if="editModale" class="modal"></div>
    <!-- fin Modale -->
    <div :class="floutage" class="bigContainer" v-if="role != 'visiteur'">
      <div class="container" v-for="(element, indexo) in listes" :key="indexo">
        <CreationTache
          v-on:modaleTache="this.visibilite[this.indexModale] = $event"
          v-on:envoieIndex="saveIndex = $event"
          v-on:watchNull="watchIndex = $event"
          :id="element.id"
          :index="indexo"
          :watchIndex="watchIndex"
        ></CreationTache>

        <ul
          class="miniContainer"
          @drop="onDrop($event, element.id)"
          @dragenter.prevent
          @dragover.prevent
        >
          <div class="containerTitre">
            <div :class="voir[element.id]">
              {{ element.titre_list }}
            </div>
            <i
              :class="voir[element.id]"
              class="fa-solid fa-ellipsis"
              @click="showOptionListe(element.id)"
            ></i>
          </div>
          <div class="modalBis" v-show="showModale[element.id]">
            <ModifTitre
              :titre="element.titre_list"
              :idContent="element.id"
              :content="'liste'"
            >
            </ModifTitre>
            <DeleteTaches
              v-on:closePopup="this.showModale[element.id] = $event"
              :idContent="element.id"
              :content="'liste'"
            ></DeleteTaches>
          </div>
          <div class="modalBis" v-show="showModale[element.id]">
            <ul></ul>
          </div>
          <div class="containerTache">
            <li
              v-for="(e, inde) in element.list_taches"
              :key="inde"
              @dragstart="startDrag($event, e)"
              draggable="true"
              class="livisi"
              :value="e.index"
            >
              <p class="tache">{{ e.titre_tache }}</p>

              <i
                class="fa-solid fa-ellipsis"
                @click="showOption(e.id, e.titre_tache)"
              ></i>
              <!-- </div> -->
              <div class="modalBis" v-show="popup[e.id]"></div>
              <div class="modalBiss" v-show="popup[e.id]">
                <ul>
                  <ModifTitre
                    :titre="titreTache"
                    :idContent="idModale"
                    :content="'tache'"
                  ></ModifTitre>
                  <DeleteTaches
                    v-on:closePopup="this.popup[this.idModale] = $event"
                    :idContent="idModale"
                    :content="'tache'"
                  ></DeleteTaches>
                  <AjoutFichier :tache_id="e.id"></AjoutFichier>
                  <DownloadFichier :tache_id="e.id"></DownloadFichier>
                </ul>
              </div>
            </li>
          </div>
        </ul>
      </div>
    </div>
    <!-- visteur -->
    <div v-else :class="floutage" class="bigContainer">
      <div class="container" v-for="(element, indexo) in listes" :key="indexo">
        <ul class="miniContainer">
          <div class="containerTitre">
            <div>
              {{ element.titre_list }}
            </div>
          </div>
          <div class="containerTache">
            <li
              v-for="(e, inde) in element.list_taches"
              :key="inde"
              class="livisi"
              :value="e.index"
            >
              <p>{{ e.titre_tache }}</p>
              <i
                class="fa-solid fa-ellipsis"
                @click="showOption(e.id, e.titre_tache)"
              ></i>
              <div class="modalBiss" v-show="popup[e.id]">
                <ul>
                  <DownloadFichier :tache_id="e.id"></DownloadFichier>
                </ul>
              </div>
            </li>
          </div>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import DeleteTachesComponent from "../ModaleDetailsProjet/DeleteTachesComponent.vue";
import CreationListeComponent from "../ModaleDetailsProjet/CreationListeComponent.vue";
import CreationTacheComponent from "../ModaleDetailsProjet/CreationTacheComponent.vue";
import ModifTitreComponent from "../ModaleDetailsProjet/ModifTitreComponent.vue";
import AjoutFichierComponent from "../ModaleDetailsProjet/AjoutFichierComponent.vue";
import DownloadFichierComponent from "../ModaleDetailsProjet/DownloadFichierComponent.vue";
export default {
  components: {
    DeleteTaches: DeleteTachesComponent,
    CreationListe: CreationListeComponent,
    CreationTache: CreationTacheComponent,
    ModifTitre: ModifTitreComponent,
    AjoutFichier: AjoutFichierComponent,
    DownloadFichier: DownloadFichierComponent,
  },
  props: {
    listes: Object,
    role: String,
    nomProjet: String,
  },
  data() {
    return {
      visibilite: [],
      titreTache: "",
      editModale: false,
      popup: [],
      idModale: 0,
      indexModale: "",
      watchIndex: null,
      saveIndex: "",
      showModale: [],
      idModaleListe: "",
      showCreationListe: false,
      floutage: "",
      voir: [],
    };
  },
  beforeMount() {
    this.connexionServeur();
    // this.voir[this.element.id] = true;
  },
  methods: {
    closeModale() {
      this.watchIndex = this.saveIndex;
      this.visibilite[this.indexModale] = false;
    },
    async requeteTempsReel() {
      const res = await axios.get(`api/projet/${this.redisIdprojet}`);
      console.log(res, "la response de requete redis");
      console.log(res.data.projet.role);
      this.$emit("changeRole", res.data.projet);
      ///////rajouter le leave du serveur redis //////
    },
    connexionServeur() {
      let id = localStorage.getItem("idProjet");
      window.demo = window.Echo.join(`projet${id}`)
        .here((users) => {
          console.log("users", users);
        })
        .joining((user) => {
          console.log(user, "join");
        })
        .leaving((user) => {
          console.log(user, "leave");
        })
        .listen(".role-projet", (event) => {
          console.log("even", event);
          this.redisIdprojet = event.message;
          this.requeteTempsReel();
        })
        .listen(".move-tache", (event) => {
          console.log(event, "move card");
          const lastListess = this.listes.find(
            (e) => e.id === event.lastListes.id
          );
          lastListess.list_taches = event.lastListes.list_taches;
          const listess = this.listes.find((e) => e.id === event.listes.id);
          listess.list_taches = event.listes.list_taches;
        })
        .listen(".ajout-tache", (event) => {
          console.log(event, "ajout taches");
          let liste = this.listes.find((e) => e.id === event.tache.list_id);
          liste.list_taches.push(event.tache);
        })
        .listen(".delete-tache", (event) => {
          console.log(event, "delete taches");
          let liste = this.listes.find((e) => e.id === event.listes.id);
          liste.list_taches = event.listes.list_taches;
          this.popup[this.idModale] = false;
        })
        .listen(".update-tache", (event) => {
          console.log(event, "uopdate taches");
          const liste = this.listes.find((e) => e.id === event.tache.list_id);
          let listeTache = liste.list_taches.find(
            (e) => e.id === event.tache.id
          );
          listeTache.titre_tache = event.tache.titre_tache;
        })
        .listen(".update-liste", (event) => {
          console.log(event, "update liste");
          let liste = this.listes.find((e) => e.id === event.liste.id);
          liste.titre_list = event.liste.titre_list;
        })
        .listen(".ajout-liste", (event) => {
          console.log(event, "ajout liste");
          this.listes.push(event.liste);
        })
        .listen(".delete-liste", (event) => {
          console.log(event, "delete-liste");
          console.log(this.listes);
          let index = this.listes.findIndex((e) => e.id === event.listes.id);
          console.log(index);
          this.listes.splice(index, 1);
          this.showModale[this.idModaleListe] = false;
        })
        .listenForWhisper("test", (e) => {
          console.log("chuto", e);
        });
    },
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

    async modifPosition(tacheId, index, listeId, lastIndex, lastListeId) {
      let id = localStorage.getItem("idProjet");
      const res = await axios.put(`api/taches/position/${id}`, {
        tacheId: tacheId,
        index: index,
        listeId: listeId,
        lastIndex: lastIndex,
        lastListeId: lastListeId,
      });
    },
    showOptionListe(id) {
      this.voir[id] = "cache";
      this.idModaleListe = id;
      this.showModale[id] = true;
    },
    showModaleListe() {
      this.voir[this.idModaleListe] = true;
      this.showModale[this.idModaleListe] = false;
    },
    showOption(id, titreTache) {
      this.titreTache = titreTache;
      this.idModale = id;
      this.popup[id] = true;
      // console.log(index);
      // this.editModale = true;
    },
    // ajoutListe(event) {},
    showCreationListeModale() {
      this.floutage = "d";
      this.showCreationListe = false;
      this.$emit("floutage", "noFloutage");
    },
    showCreationListeFloutage() {
      this.floutage = "floutage";
      this.showCreationListe = true;
      this.$emit("floutage", "floutage");
    },
  },
};
</script>

<style scoped>
.cache {
  display: none;
}
.floutage {
  filter: blur(8px);
}
.buttonListe {
  text-transform: uppercase;
  font-size: 0.5rem;
  font-family: "Lexend Mega", sans-serif;
  background-color: #1ea3dc;
  color: white;
  border-radius: 6.25em;
  border: none;
  cursor: pointer;
  margin-bottom: 6px;
  margin-bottom: 6px;
  padding-left: 2rem;
  padding-right: 2rem;
  padding-top: 3px;
  padding-bottom: 3px;
}
.containerTitre .fa-solid {
  color: white;
  margin-right: 10px;
  cursor: pointer;
}
.livisi .fa-solid {
  color: #39dee0;
  margin-right: 10px;
  cursor: pointer;
}

.containerCreation {
  display: flex;
  justify-content: space-between;
  margin-left: 5%;
  margin-right: 5%;
  background: #39dee0;
  padding: 13px;
  border-radius: 10px;
  align-items: center;
  margin-top: 45px;
}
.containerCreation .containerRoleNom {
  display: flex;
  flex-direction: column;
}
.containerCreation .containerRoleNom div {
  font-size: 0.6rem;
  font-family: "Lexend Mega", sans-serif;
  color: black;
  text-transform: uppercase;
  margin-bottom: 3px;
}
.bigContainer {
  display: flex;
  margin-left: 5%;
  margin-top: 45px;
  overflow: auto;
  scrollbar-color: #39dee0 white;
}
.bigContainer .container {
  margin-right: 20px;
}
.containerTache {
  background-color: white;
  border-radius: 10px;
}
.livisi {
  height: 30px;
  position: relative;
  background-color: #fff;
  border-radius: 5px;
  display: flex;
  justify-content: space-between;

  align-items: center;
}
.livisi p {
  margin: Opx;
  color: black;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.7em;
  overflow: hidden;
  text-overflow: ellipsis;
}

.miniContainer {
  background-color: #39dee0;
  list-style: none;
  width: 7rem;
  padding-left: 2rem;
  border-radius: 10px;
  margin-top: 8px;
}
.miniContainer .containerTitre {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.containerTitre div {
  text-transform: uppercase;
  overflow: hidden;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.7em;
  margin-top: 4px;
  margin-bottom: 4px;
  text-overflow: ellipsis;
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
  height: 100%;
  cursor: pointer;
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
  cursor: pointer;
}
.modale-overlay-tache {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
  cursor: pointer;
}
.modalBis {
  z-index: 99;
  margin: 0px;
  position: relative;
}
.modalBis ul {
  padding: 0px;
  list-style: none;
  text-align: center;
}
.modalBis ul li {
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
}
.modalBiss {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 150;
  width: 75vw;
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;
  padding: 25px;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 120px;
}
.modalBiss ul {
  list-style: none;
  padding: 0px;
}

/* .modalBiss input {
  width: 50%;
} */
.modale {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 150;
  width: 75vw;
  max-width: 600px;
  background-color: #fff;
  border-radius: 16px;
  padding: 25px;
}
@media only screen and (min-width: 1200px) {
  .containerCreation {
    margin-left: 15%;
    margin-right: 15%;
    height: 10vh;
  }
  .buttonListe {
    font-size: 0.9rem;
  }
  .containerCreation .containerRoleNom div {
    font-size: 0.9rem;
  }
}
</style>