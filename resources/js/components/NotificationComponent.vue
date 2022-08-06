<template>
  <div class="bigContainer">
    <div class="container">
      <h3>Hote</h3>

      <div v-for="(element, index) in response.hote" :key="index">
        <!-- <div v-if="showHote">
          <input type="hidden" :value="(backgroundColorHote[index] = 'blue')" />
        </div> -->
        <div class="containerHote">
          <p :style="{ color: 'blue' }" @click="changeColorHote(element.id)">
            Vous avez invité {{ element.guests.nom }} au projet
            {{ element.projets.nom }} son status est {{ element.status }}
          </p>
        </div>
      </div>
    </div>
    <div>
      <h3>invitation</h3>
      <div v-for="(element, index) in response.invite" :key="index">
        <div class="containerInvite">
          <div v-if="element.status == 'pending'">
            <p
              :style="{ color: element.color }"
              @click="changeColorInvite(element.id, element.color)"
            >
              {{ element.admins.nom }} vous a invité au projet
              {{ element.projets.nom }} votre status est {{ element.status }}
            </p>
            <i
              @click="
                acceptInvitation(
                  element.projet_id,
                  element.id,
                  element.admin_id
                )
              "
              class="fa-solid fa-check"
            ></i>
            <i
              class="fa-solid fa-xmark"
              @click="
                refuseInvitation(
                  element.projet_id,
                  element.id,
                  element.admin_id
                )
              "
            ></i>
          </div>
          <div v-else>
            <p
              :style="{ color: element.color }"
              @click="changeColorInvite(element.id, element.color)"
            >
              {{ element.admins.nom }} vous a invité au projet
              {{ element.projets.nom }} votre status est {{ element.status }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const NotificationComponent = {
  data() {
    return {
      response: {
        hote: "",
        invite: "",
      },
      status: "",
      showHote: true,
      showInvite: true,
    };
  },
  beforeMount() {
    this.dataInvitation();
    this.tempsReel();
  },

  methods: {
    tempsReel() {
      window.Echo.private("App.Models.User." + User.id).notification(
        (notification) => {
          console.log(notification, "hellodahadad");
          if (notification.invitationAdmin != null) {
            return false;
          }
          this.response.invite.push(notification.invitationInvit);
        }
      );
    },
    async dataInvitation() {
      const res = await axios.get("api/invitation");
      console.log(res, "le res");
      this.response.invite = res.data.invite;
      this.response.hote = res.data.hote;
      this.joinChannel();
    },
    joinChannel() {
      this.response.invite.forEach((element) => {
        window.Echo.private(`invitation${element.id}`).listen(
          ".invitation",
          (e) => {
            console.log(e, "hello invit");
          }
        );
      });
      this.response.hote.forEach((element) => {
        window.Echo.private(`invitation${element.id}`).listen(
          ".invitation",
          (e) => {
            console.log(e, "hello admin");
            let test = this.response.hote.find(
              (element) => element.id === e.idInvit
            );
            console.log(test);
            test.status = e.status;
            console.log(this.response.hote, "je suis hote");
          }
        );
      });
    },

    acceptInvitation(id, idInvit, adminId) {
      this.status = "accept";
      this.responseInvit(id, idInvit, adminId);
    },
    async responseInvit(id, idInvit, adminId) {
      const res = await axios.put(`api/invitation/${id}`, {
        invitation: this.status,
        idInvit: idInvit,
        adminId: adminId,
      });
      console.log(res);
      if (res.status === 200) {
        let projet = this.response.invite.find(
          (element) => element.projet_id === res.data.idProjet
        );
        projet.status = res.data.status;
      }
    },
    refuseInvitation(id, idInvit, adminId) {
      this.status = "refuse";
      this.responseInvit(id, idInvit, adminId);
    },
    changeColorHote(id) {
      this.luNotif(id);
    },
    changeColorInvite(id, color) {
      console.log(color, "couleur");
      if (color != "blue") {
        return false;
      }
      console.log("changeColor");
      this.luNotif(id);
    },
    async luNotif(id) {
      const res = await axios.put("api/notifications", { id: id });
      console.log(res, "changeColorInvite");
      if (res.status === 200) {
        let color = this.response.invite.find(
          (element) => element.id === res.data.id
        );
        color.color = "";
        console.log(this.response.invite);
        let retrait = 1;
        console.log(retrait);
        this.$store.dispatch("envoieNbr", retrait);
      }
    },
  },
};
export default NotificationComponent;
</script>

<style lang="scss" scoped>
.bigContainer {
  display: grid;
  grid-template-columns: 3fr 3fr 1fr;
  grid-auto-rows: 1fr 1fr;
  grid-template-areas:
    "a1 a2 a3"
    "b1 b2 b3";
  height: 85vh;
}
.fa-solid {
  margin-right: 3px;
  margin-left: 3px;
}
// .containerHote {
//   background-color: rgb(108, 108, 255);
// }
// .containerInvite {
//   background-color: rgb(108, 108, 255);
// }
</style>