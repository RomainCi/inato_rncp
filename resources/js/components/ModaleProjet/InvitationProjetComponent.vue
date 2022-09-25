<template>
  <div>
    <div class="mainContainer">
      <p>INVITATION</p>
      <form @submit.prevent="invitForm">
        <label for="email">EMAIL</label>
        <input type="email" v-model="invitation.email" />
        <button>valider</button>
      </form>
    </div>
  </div>
</template>

<script>
const InvitationProjetComponent = {
  props: {
    idProjet: Number,
  },
  data() {
    return {
      modaleInvit: false,
      invitation: {
        email: "",
        id: this.idProjet,
      },
    };
  },
  mounted() {
    this.$emit("floutage", "floutage");
  },
  methods: {
    async invitForm() {
      console.log(this.invitation.id, "id");
      console.log(this.invitation.email, "email invit");
      const res = await axios.post("api/invitation", this.invitation);
      console.log(res, "inviation");
    },
  },
};
export default InvitationProjetComponent;
</script>

<style scoped>
.mainContainer {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  filter: blur(0px) !important;
}
.mainContainer p {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
}
.mainContainer form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-around;
  width: 100%;
  gap: 15px;
}
.mainContainer form input {
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
  width: 100%;
}
.mainContainer form button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  align-self: center;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
  border: none;
  cursor: pointer;
}
.mainContainer form label {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
  font-size: 0.9em;
}
</style>