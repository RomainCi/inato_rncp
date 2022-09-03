<template>
  <div>
    {{ role }}
    <div v-if="role != 'visiteur'">
      <CreationListe></CreationListe>
    </div>
    <!-- Modale -->
    <div
      class="modal-overlay"
      v-show="editModale"
      @click="editModale = false"
    ></div>
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
      @click="showModale[this.idModaleListe] = false"
    ></div>
    <div v-if="editModale" class="modal"></div>
    <!-- fin Modale -->
    <div class="bigContainer" v-if="role != 'visiteur'">
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
          {{
            element.titre_list
          }}<i
            class="fa-solid fa-ellipsis-vertical"
            @click="showOptionListe(element.id)"
          ></i>
          <div class="modalBiss" v-show="showModale[element.id]">
            <ModifTitre
              :titre="element.titre_list"
              :idContent="element.id"
              :content="'liste'"
            >
            </ModifTitre>
          </div>
          <div class="modalBis" v-show="showModale[element.id]">
            <ul>
              <DeleteTaches
                v-on:closePopup="this.showModale[element.id] = $event"
                :idContent="element.id"
                :content="'liste'"
              ></DeleteTaches>
            </ul>
          </div>
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
              <ModifTitre
                :titre="titreTache"
                :idContent="idModale"
                :content="'tache'"
              ></ModifTitre>
            </div>
            <div class="modalBis" v-show="popup[e.id]">
              <ul>
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
        </ul>
      </div>
    </div>
    <!-- visteur -->
    <div v-else class="bigContainer">
      <div class="container" v-for="(element, indexo) in listes" :key="indexo">
        <ul class="miniContainer">
          {{
            element.titre_list
          }}
          <li
            v-for="(e, inde) in element.list_taches"
            :key="inde"
            class="livisi"
            :value="e.index"
          >
            {{ e.titre_tache
            }}<i
              class="fa-solid fa-ellipsis-vertical"
              @click="showOption(e.id, e.titre_tache)"
            ></i>
            <div class="modalBis" v-show="popup[e.id]">
              <ul>
                <DownloadFichier :tache_id="e.id"></DownloadFichier>
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
    };
  },
  beforeMount() {
    this.connexionServeur();
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
      this.idModaleListe = id;
      this.showModale[id] = true;
    },
    showOption(id, titreTache) {
      this.titreTache = titreTache;
      this.idModale = id;
      this.popup[id] = true;
      // console.log(index);
      // this.editModale = true;
    },
    // ajoutListe(event) {},
  },
};
</script>

<style scoped>
.bigContainer {
  display: flex;
  position: absolute;
}
.livisi {
  background-color: red;
  height: 30px;
  position: relative;
}

.miniContainer {
  position: relative;
  background-color: green;
  margin-left: 2px;
  margin-right: 2px;
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
.modale-overlay-tache {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
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