<template>
  <div>
    <!-- modale close verifie la height -->

    <div
      class="modale-overlay"
      v-show="showModaleBis"
      @click="modaleProjet"
    ></div>
    <div class="modal-overlay" v-show="modaleUser" @click="modaleUserF"></div>

    <div
      class="modal-overlay"
      v-show="modaleInvit"
      @click="modaleInvitationF"
    ></div>

    <div
      class="modal-overlay"
      v-show="modaleDelete"
      @click="modaleDeleteF"
    ></div>
    <div
      class="modal-overlay"
      v-show="modaleQuitter"
      @click="modaleQuitter = false"
    ></div>
    <!-- <div class="close" @click="modaleInvit = false">&#x2715;</div> -->

    <!-- modale close -->

    <CreationProjet
      v-on:ajoutProjet="ajoutProjet($event)"
      v-on:floutage="ajoutFloutage($event)"
      v-on:noFloutage="enleverFloutage($event)"
      :class="floutagee"
    ></CreationProjet>

    <div class="bigAllContainer">
      <div v-if="modaleInvit" class="modal">
        <InvitationProjet
          v-on:floutage="ajoutFloutageInvit($event)"
          :idProjet="idProjet"
        ></InvitationProjet>
      </div>
      <div v-if="modaleDelete" class="modal">
        <deleteProjet :idDelete="idDelete"></deleteProjet>
      </div>
      <div v-if="modaleUser" class="modal">
        <GestionUser
          :projetId="idProjet"
          :indexProjet="saveIndex"
          :active="active"
        ></GestionUser>
      </div>

      <div
        class="bigContainer"
        v-for="(element, index) in response.projet"
        :key="index"
        :class="floutage"
      >
        <div class="container" :class="showBlock[index]">
          <div class="miniContainer">
            <p>
              <span>{{ element.titre }}</span
              ><br />{{ element.role }}
            </p>
            <img @click="enterProjet(element.id)" src="../assets/enter.png" />
          </div>
          <i class="fa-solid fa-ellipsis" @click="showOption(index)"></i>
          <div
            :style="{
              'background-image': `url(${element.urlprivate})`,
            }"
            class="backgroundProjet"
          ></div>
        </div>

        <div
          v-if="element.role != 'admin'"
          class="modalBis"
          v-show="popup[index]"
        >
          <ul>
            <li @click="quitterProjet(element.id, index)">QUITTER</li>
          </ul>
        </div>

        <div
          v-if="element.role == 'admin'"
          class="modalBis"
          v-show="popup[index]"
        >
          <ul>
            <li @click="modaleDeleteProjet(element.id, index)">SUPPRIMER</li>
            <li @click="modaleInvitation(element.id, index)">INVITER</li>
            <li @click="modaleGestionUser(element.id, index)">ROLES</li>
            <li @click="quitterProjet(element.id, index)">QUITTER</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import GestionUserComponent from "./ModaleProjet/GestionUserComponent.vue";
import CreationProjetComponent from "./ModaleProjet/CreationProjetComponent.vue";
import InvitationProjetComponent from "./ModaleProjet/InvitationProjetComponent.vue";
import DeleteProjetComponent from "./ModaleProjet/DeleteProjetComponent.vue";

import router from "../router";

const ProjetComponent = {
  components: {
    GestionUser: GestionUserComponent,
    CreationProjet: CreationProjetComponent,
    InvitationProjet: InvitationProjetComponent,
    deleteProjet: DeleteProjetComponent,
  },
  props: {},
  data() {
    return {
      response: [],
      popup: [""],
      showModaleBis: false,
      saveIndex: "",
      modaleInvit: false,
      modaleUser: false,
      modaleDelete: false,
      modaleQuitter: false,
      invitation: {
        email: "",
        id: "",
      },
      idProjet: "",
      redisIdprojet: null,
      active: false,
      idDelete: "",
      floutage: "",
      floutagee: "",
      showBlock: [""],
    };
  },
  beforeMount() {
    this.dataProjet();
  },
  mounted() {},

  methods: {
    ////requete/////////////
    async requeteTempsReel() {
      const res = await axios.get(`api/projet/${this.redisIdprojet}`);
      console.log(res, "la response de requete redis");
      if (res.data.projet === 0) {
        console.log(this.response, "varaiable response");
        let index = this.response.projet.findIndex(
          (element) => element.id === this.redisIdprojet
        );
        this.response.projet.splice(index, 1);
        window.Echo.leave(`projet${this.redisIdprojet}`);
      } else {
        let projet = this.response.projet.find(
          (element) => element.id === this.redisIdprojet
        );
        projet.role = res.data.projet.role;
      }
      if (this.idProjet === this.redisIdprojet) {
        this.active = !this.active;
      }
      ///////rajouter le leave du serveur redis //////
    },
    async dataProjet() {
      const res = await axios.get("api/projet");
      this.response = res.data;
      console.log("projet", this.response);
      console.log("ccoucou");

      this.joinChannel();
    },

    async invitForm() {
      console.log(this.invitation.id, "id");
      console.log(this.invitation.email, "email invit");
      const res = await axios.post("api/invitation", this.invitation);
      console.log(res, "inviation");
    },
    async quitterProjet(id, index) {
      const res = await axios.post(`api/projet/quitter/${id}`, {
        index: index,
      });
      console.log(res);
    },
    //////////fin requete//////////
    joinChannel() {
      if (this.response.projet == null) {
        return false;
      }
      this.response.projet.forEach((element) => {
        console.log("je rentre dans le channel projet");
        console.log(element.id);
        window.demo = window.Echo.join(`projet${element.id}`)
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
            console.log("event dazdzad", event);
            console.log(this.response, "la reponse");
            this.redisIdprojet = event.message;
            console.log(this.redisIdprojet, "idprojet redis");
            this.requeteTempsReel();
          })
          .listenForWhisper("test", (e) => {
            console.log("chuto", e);
          });
      });
    },
    ajoutProjet(event) {
      console.log(event, "event");
      this.dataProjet();
      // console.log(this.response, "ajout projet event");
      // this.response.projet.push(event, "push");
      // this.joinChannel();
    },
    showOption(index) {
      console.log(index, "hey");
      this.popup[index] = true;
      this.saveIndex = index;
      this.showModaleBis = true;
      this.showBlock[index] = "cache";
    },
    modaleDeleteProjet(id, index) {
      this.idDelete = id;
      this.modaleDelete = true;
      this.popup[index] = false;
      this.showBlock[index] = "d";
      this.floutage = "floutage";
      this.floutagee = "floutage";
      this.$emit("floutage", "floutage");
    },
    modaleProjet() {
      this.popup[this.saveIndex] = false;
      this.showModaleBis = false;
      this.showBlock[this.saveIndex] = "d";
    },
    modaleInvitation(id, index) {
      this.idProjet = id;
      this.modaleInvit = true;
      this.popup[index] = false;
      this.showBlock[index] = "d";
      this.showModaleBis = false;
    },
    modaleGestionUser(id, index) {
      console.log(id);
      this.idProjet = id;
      this.modaleUser = true;
      this.popup[index] = false;
      this.showBlock[index] = "d";
      this.floutage = "floutage";
      this.floutagee = "floutage";
      this.$emit("floutage", "floutage");
    },
    modaleInvitationF() {
      this.floutage = "d";
      this.floutagee = "d";
      this.modaleInvit = false;
      this.$emit("noFloutage", "noFloutage");
    },
    modaleDeleteF() {
      this.floutage = "d";
      this.floutagee = "d";
      this.modaleDelete = false;
      this.$emit("noFloutage", "noFloutage");
    },
    modaleUserF() {
      this.floutage = "d";
      this.floutagee = "d";
      this.modaleUser = false;
      this.$emit("noFloutage", "noFloutage");
    },
    ajoutFloutage(e) {
      this.floutage = e;
      this.$emit("floutage", "floutage");
    },
    ajoutFloutageInvit(e) {
      this.floutage = e;
      this.floutagee = e;
      this.$emit("floutage", "floutage");
    },
    enleverFloutage(e) {
      this.floutage = e;
      this.$emit("noFloutage", "noFloutage");
    },
    enterProjet(id) {
      this.$router.push("/detailsProjet");
      localStorage.setItem("idProjet", id);
    },
  },
};
export default ProjetComponent;
</script>

<style lang="scss" scoped>
i {
  color: white;
  align-self: flex-end;
  margin-right: 4px;
  font-size: 25px;
  z-index: 1;
}
.floutage {
  filter: blur(8px);
}
.bigAllContainer {
  display: flex;
  margin-left: 5%;
  margin-top: 20px;
  margin-right: 5%;
  flex-wrap: wrap;
  justify-content: space-between;
}
.bigContainer {
  // position: relative;
  display: flex;
  flex-direction: row;
}
.container {
  position: relative;
  display: flex;
  height: 100px;
  width: 100px;
  overflow: hidden;
  flex-direction: column;
  justify-content: space-between;
  border-radius: 7px;
  margin-bottom: 20px;
}
.container img {
  height: 1.5em;
  width: 1.5em;
  margin-right: 20px;
  cursor: pointer;
}
.container img:hover {
  filter: invert(57%) sepia(74%) saturate(3417%) hue-rotate(159deg)
    brightness(91%) contrast(103%);
}
.container p {
  margin: 0px;
  text-transform: uppercase;
  font-size: 8px;
  font-family: "Lexend Mega", sans-serif;
  color: black;
  text-overflow: ellipsis;
  overflow: hidden;
  margin-left: 4px;
}
.container .miniContainer {
  display: flex;
  margin-top: 10px;
  margin-left: 10x;
  justify-content: space-between;
  z-index: 1;
}
.container .miniContainer span {
  font-size: 10px;
  text-overflow: ellipsis;
}
.backgroundProjet {
  position: absolute;
  left: 0;
  top: 0;
  width: 100px;
  height: 100px;
  opacity: 0.5;
  content: "";
  background-size: cover;
}

.back {
  height: 60px;
  width: 60px;
  background-size: cover;
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
.modale-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  cursor: pointer;
}

.modal {
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
li {
  list-style: none;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.7rem;
  margin-top: 2px;
  margin-bottom: 2px;
  color: white;
  cursor: pointer;
  margin-left: 1px;
}
li:hover {
  color: rgb(81, 249, 255);
}
ul {
  padding: 0px;
}
.modalBis {
  // position: relative;
  top: 0%;
  left: 0%;
  z-index: 99;
  height: 100px;
  width: 100px;
  background-color: #0964d7;
  border-radius: 7px;
  margin-bottom: 20px;
}
.cache {
  display: none;
}
@media only screen and (min-width: 1200px) {
  .container {
    height: 150px;
    width: 150px;
  }
  .modalBis {
    height: 150px;
    width: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .backgroundProjet {
    height: 150px;
    width: 150px;
  }
  .bigAllContainer {
    margin-left: 15%;
    margin-right: 15%;
    justify-content: space-between !important;
  }
  li {
    font-size: 1rem;
    margin-left: 3px;
  }
  .container .miniContainer span {
    font-size: 13px;
  }
  .container img {
    height: 2rem;
    width: 2rem;
  }
}
@media only screen and (min-width: 900px) {
  .bigAllContainer {
    justify-content: space-evenly;
  }
}
</style>

