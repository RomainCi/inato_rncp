<template>
  <div>
    <div v-if="response.length == 0">
      <p>AUCUN COLLABORATEUR</p>
    </div>
    <div v-else class="mainContainer">
      <p>GESTION DES COLLABORATEURS</p>
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
              class="select"
              v-model="myValue[indexx]"
              :options="myOptions"
              :settings="{ width: '100%', color: 'red' }"
            />
            <input type="hidden" :value="(userId[indexx] = value.user_id)" />
          </td>
        </tr>
      </table>
      <button @click="valider()">valider</button>
    </div>
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
      console.log(this.response, "response []");
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

<style scoped>
.mainContainer {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
p {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
  font-size: 0.8rem;
}
.mainContainer table,
th,
tr,
td {
  border-collapse: collapse;
  border: 3px solid #0d85bc;
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
}
button {
  margin-top: 20px;
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
.select {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
}
.select2-container--default {
  color: red;
}
li {
  color: red;
}
.select2-results {
  background-color: red !important;
}
.select2-search {
  background-color: red;
}
#select2--result-vn89-admin {
  color: red;
}
.select2-results__option {
  color: red;
  background-color: red;
}
</style>