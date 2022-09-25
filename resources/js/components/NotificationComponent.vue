<template>
  <div class="bigMain">
    <div class="containerImage">
      <p>notifications</p>
      <img src="../assets/notification.png" alt="" />
    </div>
    <div class="containerMain">
      <h4>invitations</h4>
      <div v-for="(element, index) in response" :key="index">
        <div
          class="condition"
          v-if="typeof element.notification.notif == 'string'"
        >
          {{ (hote = "lu") && (invite = "lu") }}
        </div>
        <div class="condition" v-else>
          {{ (hote = "nonLu") && (invite = "nonLu") }}
        </div>
        <div
          class="allInvitation"
          v-for="(e, i) in element.invitation"
          :key="i"
        >
          <div v-if="e.admin_id == myId">
            <p @click="luNotif(e.id, element.notification.notif)" :class="hote">
              Vous avez invitez {{ e.guests.nom }} au projet
              {{ e.projets.nom }} son status est {{ e.status }}
            </p>
            <!-- <button @click="deleteInvitation(e.id)">supprimer</button> -->
          </div>
          <div class="containerFlex" v-else>
            <p
              @click="luNotif(e.id, element.notification.notif)"
              :class="invite"
            >
              {{ e.admins.nom }} vous a invité au projet
              {{ e.projets.nom }} votre status est {{ e.status }}
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
          </div>
          <div class="butonDelete">
            <button @click="deleteInvitation(e.id)">supprimer</button>
          </div>
        </div>
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
        console.log(this.response, "supprimer this.response");
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
          if (notification.invitationAdmin != null) {
            return false;
          }
          this.response.push(notification.invite);
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
      console.log(res, "le res responseInvit");
      if (res.status === 200) {
        console.log(this.response, "1");

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
.fa-solid {
  cursor: pointer;
}
.fa-solid:hover {
  color: #1ea3dc;
}
h4 {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9rem;
  color: #1ea3dc;
  text-transform: uppercase;
  margin: 0px;
  margin-bottom: 10px;
}
.containerFlex {
  display: flex;
  // justify-content: space-between;
}
.containerMain {
  margin-top: 45px;
  margin-left: 5%;
  margin-right: 5%;
  background: white;
  border-radius: 10px;
  padding: 13px;
  height: 280px;
  overflow: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.condition {
  display: none;
}
.lu {
  color: black;
  font-size: 0.6rem;
  margin: 0px;
  font-family: "Lexend Mega", sans-serif;
  align-self: center;
}
.nonLu {
  color: #1ea3dc;
  font-size: 0.6rem;
  margin: 0px;
  font-family: "Lexend Mega", sans-serif;
  align-self: center;
  cursor: pointer;
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
.butonDelete {
  display: flex;
  justify-content: center;
  margin-bottom: 10px;
}
.containerImage {
  display: none;
}
button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.6rem;
  /* align-self: center; */
  border-radius: 6.25em;
  padding-right: 1.3em;
  padding-left: 1.3em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
  text-transform: uppercase;
  text-align: center;
}
@media only screen and (min-width: 650px) {
  .lu {
    font-size: 0.7rem;
  }
  .nonLu {
    font-size: 0.7rem;
  }
  button {
    font-size: 0.7rem;
  }
}
@media only screen and (min-width: 800px) {
  .lu {
    font-size: 0.9rem;
  }
  .nonLu {
    font-size: 0.9rem;
  }
  button {
    font-size: 0.9rem;
  }
}
@media only screen and (min-width: 1100px) {
  .containerMain {
    width: 40%;
    height: 430px;
    margin-right: 15%;
  }
  .containerImage {
    display: block;
    margin-top: 45px;
    margin-left: 15%;
    align-self: center;
  }
  .containerImage img {
    height: 300px;
    width: 300px;
  }
  .containerImage p {
    color: white;
    font-family: "Lexend Mega", sans-serif;
    font-size: 1.3em;
    text-transform: uppercase;
    position: relative;
    top: 236px;
    left: 119px;
    width: fit-content;
  }
  .bigMain {
    display: flex;
    justify-content: space-between;
  }
}
</style>