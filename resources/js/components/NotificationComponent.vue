<template>
  <div v-for="(element, index) in response" :key="index">
    <div class="condition" v-if="typeof element.notification.notif == 'string'">
      {{ (hote = "lu") && (invite = "lu") }}
    </div>
    <div class="condition" v-else>
      {{ (hote = "nonLu") && (invite = "nonLu") }}
    </div>
    <div v-for="(e, i) in element.invitation" :key="i">
      <div v-if="e.admin_id == myId">
        <p @click="luNotif(e.id, element.notification.notif)" :class="hote">
          Vous avez invitez {{ e.guests.nom }} au projet {{ e.projets.nom }} son
          status est {{ e.status }}
        </p>
        <button @click="deleteInvitation(e.id)">supprimer</button>
      </div>
      <div v-else>
        <p @click="luNotif(e.id, element.notification.notif)" :class="invite">
          {{ e.admins.nom }} vous a invité au projet {{ e.projets.nom }} votre
          status est {{ e.status }}
        </p>
        <div v-if="e.status == 'pending'">
          <i
            @click="acceptInvitation(e.projet_id, e.id, e.admin_id)"
            class="fa-solid fa-check"
          ></i>
          <i
            class="fa-solid fa-xmark"
            @click="refuseInvitation(e.projet_id, e.id, e.admin_id)"
          ></i>
        </div>
        <button @click="deleteInvitation(e.id)">supprimer</button>
      </div>
    </div>
  </div>
  <!-- <div class="bigContainer">
    <div class="container">
      <h3>Hote</h3>
      <div v-for="(element, index) in response.hote" :key="index">
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
            <button @click="deleteInvitation(element.id)">supprimer</button>
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
  </div> -->
</template>

<script>
import axios from "axios";
const NotificationComponent = {
  data() {
    return {
      response: "",
      status: "",
      showHote: true,
      showInvite: true,
      myId: null,
      nombreLue: 0,
      nombreNonLu: 4,
    };
  },
  beforeMount() {
    this.dataInvitation();
    this.tempsReel();
  },

  methods: {
    async deleteInvitation(id) {
      const res = await axios.delete(`api/invitation/${id}`);
      console.log(res);
      if (res.data.readAt != "0") {
        let invite = this.response.find(
          (e) => e.invitation[0].id == res.data.id
        );
        invite.notification.notif = res.data.readAt;
        this.$emit("luNotif", 1);
      }
      let index = this.response.findIndex(
        (e) => e.invitation[0].id == res.data.id
      );
      console.log(index);
      this.response.splice(index, 1);
    },
    tempsReel() {
      window.Echo.private("App.Models.User." + User.id).notification(
        (notification) => {
          console.log(notification, "hellodahadad");
          if (notification.invitationAdmin != null) {
            return false;
          }
          console.log(this.response, "la raposasasa");
          this.response.push(notification.invite);
          console.log(this.response, "new respponse");
        }
      );
    },
    async dataInvitation() {
      const res = await axios.get("api/invitation");
      console.log(res, "le res");
      this.myId = res.data.id;
      this.response = res.data.invite;
      console.log(this.response, "this respose");
    },

    acceptInvitation(id, idInvit, adminId) {
      this.status = "accept";
      this.responseInvit(id, idInvit, adminId);
    },
    async responseInvit(id, idInvit, adminId) {
      console.log(id);
      const res = await axios.put(`api/invitation/${id}`, {
        invitation: this.status,
        idInvit: idInvit,
        adminId: adminId,
      });
      console.log(res);
      if (res.status === 200) {
        let invite = this.response.find(
          (e) => e.invitation[0].id == res.data.id
        );
        console.log(invite);
        invite.invitation[0].status = res.data.status;
        if (res.data.readAt == "0") {
          return false;
        } else {
          invite.notification.notif = res.data.readAt;
          this.$emit("luNotif", 1);
        }
      }
    },
    refuseInvitation(id, idInvit, adminId) {
      this.status = "refuse";
      this.responseInvit(id, idInvit, adminId);
    },
    async luNotif(id, notif) {
      console.log(this.nombreNonLu);

      if (notif != null) {
        return false;
      }
      const res = await axios.put("api/notifications", { id: id });
      console.log(res, "changeColor");
      console.log(this.response, "inviatat");
      console.log(res.data.id, "id");
      let invite = this.response.find((e) => e.invitation[0].id == res.data.id);
      invite.notification.notif = res.data.readAt;
      this.$emit("luNotif", 1);
    },
  },
};
export default NotificationComponent;
</script>

<style lang="scss" scoped>
.condition {
  display: none;
}
.lu {
  color: black;
}
.nonLu {
  color: blue;
}
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