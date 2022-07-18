<template>
  <div>
    <h2>hello invit</h2>
    {{ idProjet }}

    <form @submit.prevent="invitForm">
      <label for="email">email</label>
      <input type="email" v-model="invitation.email" />
      <button>valider</button>
    </form>
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

<style  scoped>
.close {
  display: flex;
  justify-content: flex-end;
  content: "&#10006";
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
</style>