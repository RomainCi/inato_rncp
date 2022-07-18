<template>
  <div>
    <table>
      <tr>
        <th>Nom</th>
        <th>Role</th>
        <th>Gestion Role</th>
      </tr>
      <tr class="red" v-for="(value, indexx) in response" :key="indexx">
        <td>{{ value.user_role.nom }}</td>
        <td>{{ value.role }}</td>
        <td>
          <Select2
            v-model="myValue[indexx]"
            :options="myOptions"
            :settings="{ width: '100%' }"
          />
          <input type="hidden" :value="(userId[indexx] = value.user_id)" />
        </td>
      </tr>
      <button @click="valider()">valider</button>
    </table>
  </div>
</template>

<script>
import axios from "axios";
const GestionUserComponent = {
  props: {
    projetId: Number,
    indexProjet: Number,
    active: Boolean,
  },
  data() {
    return {
      myValue: [],
      myOptions: ["admin", "editeur", "visiteur", "supprimer"],
      test: "",
      value: "",
      projetId: this.projetId,
      roleUser: [],
      userId: [],
      response: "",
      test: 1,
    };
  },
  watch: {
    active: function (neval, oldval) {
      this.projetAdmin();
      this.test = 2;
      console.log("coucou");
    },
  },
  beforeMount() {
    this.projetAdmin();
  },
  methods: {
    async projetAdmin() {
      const res = await axios.get(`api/projet/${this.projetId}/admin`);
      this.response = res.data.projetAdmin;
      console.log(this.response);
    },
    valider() {
      this.myValue.forEach((element, index) => {
        this.roleUser.push({
          user_id: this.userId[index],
          role: element,
        });
      });
      console.log(this.projetId, "idProjet");
      this.gestionRoleUser();
    },
    async gestionRoleUser() {
      const res = await axios.post(`api/projet/${this.projetId}/role`, {
        roleUser: this.roleUser,
      });
      console.log(this.roleUser, "first");
      if (res.status === 200) {
        this.roleUser = [];
      }
      console.log(this.roleUser, "last");
    },
  },
  mounted() {},
};
export default GestionUserComponent;
</script>

<style  scoped>
.red {
  background-color: red;
}
</style>