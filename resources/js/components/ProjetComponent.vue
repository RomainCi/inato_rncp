<template>
  <div class="tutu">
    <!-- modale close verifie la height -->

    <div
      class="modale-overlay"
      v-show="showModaleBis"
      @click="modaleProjet"
    ></div>
    <div
      class="modal-overlay"
      v-show="modaleUser"
      @click="modaleUser = false"
    ></div>

    <div
      class="modal-overlay"
      v-show="modaleInvit"
      @click="modaleInvit = false"
    ></div>
    <!-- <div class="close" @click="modaleInvit = false">&#x2715;</div> -->

    <!-- modale close -->

    <CreationProjet :datasPublicUrl="response.publicUrl"></CreationProjet>

    <h2>Mes projets</h2>
    <div class="bigAllContainer">
      <div v-if="modaleInvit" class="modal">
        <InvitationProjet :idProjet="idProjet"></InvitationProjet>
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
      >
        <div class="container">
          <i class="fa-solid fa-ellipsis-vertical" @click="showOption(index)">
            <div
              :style="{
                'background-image': `url(${element.urlprivate})`,
              }"
              class="backgroundProjet"
            ></div>
          </i>
          <p>{{ element.titre }}{{ index }}<br />{{ element.role }}</p>
        </div>

        <div
          v-if="element.role != 'admin'"
          class="modalBis"
          v-show="popup[index]"
        >
          <p>Vous n'avez pas les acces</p>
        </div>

        <div
          v-if="element.role == 'admin'"
          class="modalBis"
          v-show="popup[index]"
        >
          <ul>
            <li @click="deleteProjet(element.id)">supprimer</li>
            <li @click="modaleInvitation(element.id, index)">inviter</li>
            <li @click="modaleGestionUser(element.id, index)">
              gerer {{ element.id }}
            </li>
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
const ProjetComponent = {
  components: {
    GestionUser: GestionUserComponent,
    CreationProjet: CreationProjetComponent,
    InvitationProjet: InvitationProjetComponent,
  },
  props: {},
  data() {
    return {
      response: "",
      popup: [""],
      showModaleBis: false,
      saveIndex: "",
      modaleInvit: false,
      modaleUser: false,
      invitation: {
        email: "",
        id: "",
      },
      idProjet: "",
      redisIdprojet: null,
      active: false,
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
      } else {
        let projet = this.response.projet.find(
          (element) => element.id === this.redisIdprojet
        );
        projet.role = res.data.projet.role;
      }
      if (this.idProjet === this.redisIdprojet) {
        this.active = !this.active;
      }
      // this.response = res.data;
      // this.response.projet.some((e) => e.id === this.redisIdprojet)
      //   ? true
      //   : window.Echo.leave(`projet${this.redisIdprojet}`);
      // if (this.response.adminProjet == 0) {
      //   return (this.modaleUser = false);
      // } else {
      //   this.response.adminProjet.some((e) => e.projet_id === this.idProjet)
      //     ? true
      //     : (this.modaleUser = false);
      // }
    },
    async dataProjet() {
      const res = await axios.get("api/projet");
      this.response = res.data;
      console.log("testo", this.response);

      this.joinChannel();
    },

    async invitForm() {
      console.log(this.invitation.id, "id");
      console.log(this.invitation.email, "email invit");
      const res = await axios.post("api/invitation", this.invitation);
      console.log(res, "inviation");
    },
    async deleteProjet(id) {
      const res = await axios.delete(`api/projet/${id}`);
      console.log(res);
      if (res.status === 200) {
        this.dataProjet();
        this.modaleProjet();
        window.Echo.leave(`projet${id}`);
      }
    },
    //////////fin requete//////////
    joinChannel() {
      this.response.projet.forEach((element) => {
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

    showOption(index) {
      console.log(index, "hey");
      this.popup[index] = true;
      this.saveIndex = index;
      this.showModaleBis = true;
    },
    modaleProjet() {
      this.popup[this.saveIndex] = false;
      this.showModaleBis = false;
    },
    modaleInvitation(id, index) {
      this.idProjet = id;
      this.modaleInvit = true;
      this.popup[index] = false;
    },
    modaleGestionUser(id, index) {
      console.log(id);
      this.idProjet = id;
      this.modaleUser = true;
      this.popup[index] = false;
    },
  },
};
export default ProjetComponent;
</script>

<style lang="scss" scoped>
.bigAllContainer {
  display: flex;
}
.bigContainer {
  // position: relative;
  display: flex;
  flex-direction: row;
}
.container {
  position: relative;
  display: flex;
  height: 120px;
  width: 120px;

  margin: 5px;

  overflow: hidden;
}
.backgroundProjet {
  position: absolute;
  left: 0;
  top: 0;
  width: 120px;
  height: 120px;

  z-index: -1;
  opacity: 0.5;
  content: "";
  background-size: cover;
}

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
  height: 100%;
}
.modale-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
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
li {
  list-style: none;
}
ul {
  padding: 0px;
}
.modalBis {
  // position: relative;
  top: 0%;
  left: 0%;
  // transform: translate(-50%, -50%);
  z-index: 99;
  margin: 0px;
  background-color: red;
}
</style>

