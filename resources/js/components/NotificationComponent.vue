<template>
  <div class="bigContainer">
    <div class="container">
      <h3>Hote</h3>
      <div v-for="(element, index) in response.hote" :key="index">
        <p>
          Vous avez invité {{ element.prenom }} au projet {{ element.nom }} son
          status est {{ element.status }}
        </p>
      </div>
    </div>
    <div>
      <h3>invitation</h3>
      <div v-for="(element, index) in response.invite" :key="index">
        <p>status:{{ element.status }}{{ element.admins.nom }}</p>
        <i
          @click="acceptInvitation(element.projet_id)"
          class="fa-solid fa-check"
        ></i>
        <i
          class="fa-solid fa-xmark"
          @click="refuseInvitation(element.projet_id)"
        ></i>
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
    };
  },
  beforeMount() {
    this.dataInvitation();
  },
  methods: {
    async dataInvitation() {
      const res = await axios.get("api/invitation");
      console.log(res);
      this.response.invite = res.data.invite;
    },
    async acceptInvitation(id) {
      const res = await axios.put(`api/invitation/${id}`, {
        invitation: "accept",
      });
      console.log(res);
    },
    async refuseInvitation(id) {
      const res = await axios.put(`api/invitation/${id}`, {
        invitation: "refuse",
      });
      console.log(res, "refuse");
      this.response.invite = res.data.invite;
      console.log(this.response.invite, "variable resposne");
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
</style>