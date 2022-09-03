<template>
  <div>
    <div
      class="modale-overlay"
      v-show="modaleEdit"
      @click="closeModaleEdit"
    ></div>
    <div
      class="modale-overlay"
      v-show="modaleEditEmail"
      @click="closeModaleEmail"
    ></div>
    <div
      class="modale-overlay"
      v-show="modaleEditPassword"
      @click="closeModalePassword"
    ></div>
    <div
      class="modale-overlay"
      v-show="modaleDeleteUser"
      @click="closeModaleDelete"
    ></div>
    <div v-show="modaleEdit" class="modal">
      <form @submit.prevent="envoieForm">
        <label for="nom">Nom</label>
        <input type="text" v-model="nom" />
        <!-- <div v-show="v$.user.nom.$error">
          {{ this.v$.user.nom.required.$message }}
        </div> -->

        <label for="prenom">prenom</label>
        <input type="text" v-model="prenom" />
        <!-- <div v-show="v$.user.prenom.$error">
          {{ this.v$.user.prenom.required.$message }}
        </div> -->
        <button>valider</button>
      </form>
    </div>
    <div v-show="modaleEditEmail" class="modal">
      <form @submit.prevent="envoieFormEmail">
        <label for="email">email</label>
        <input type="email" v-model="email" />
        <p v-show="show == 'a'">Vous avez 5 min pour valider votre email</p>
        <button>valider</button>
      </form>
    </div>
    <div v-show="modaleEditPassword" class="modal">
      <form @submit.prevent="envoiePassword">
        <label for="ancien">ancien mot de passe</label>
        <input type="text" v-model="securite.ancien" />

        <label for="password">nouveau mot de passe</label>
        <input type="text" v-model="securite.password" />

        <label for="confirmation">confirmation du mot de passe</label>
        <input type="text" v-model="securite.password_confirmation" />

        <button>valider</button>
      </form>
    </div>
    <div v-show="modaleDeleteUser" class="modal">
      <form @submit.prevent="deleteUser">
        <label>Ecrivez supprimer le compte </label>
        <input type="text" v-model="supprimer" />
        <button>valider</button>
      </form>
    </div>
    <h2>Mes informations</h2>
    <div>
      <p>Nom: {{ user.nom }}</p>
      <p>Prenom: {{ user.prenom }}</p>
      <p @click="modaleEdit = true">Edit</p>
    </div>

    <h2>Securite</h2>
    <div>
      <p>Email : {{ user.email }}</p>
      <p @click="modaleEditEmail = true">Edit email</p>
    </div>
    <div>
      <p @click="modaleEditPassword = true">Changer de mot de passe</p>
    </div>
    <div>
      <p @click="modaleDeleteUser = true">supprimer le compte</p>
    </div>
  </div>
  <!-- <div>
    <div>
      <h2>Mes informations</h2>
      <form @submit.prevent="check">
        <label for="nom">Nom</label>
        <input type="text" v-model="user.nom" @blur="v$.user.nom.$touch" />
        <div v-show="v$.user.nom.$error">
          {{ this.v$.user.nom.required.$message }}
        </div>

        <label for="prenom">prenom</label>
        <input
          type="text"
          v-model="user.prenom"
          @blur="v$.user.prenom.$touch"
        />
        <div v-show="v$.user.prenom.$error">
          {{ this.v$.user.prenom.required.$message }}
        </div>

        <label for="email">Email</label>
        <input type="email" v-model="user.email" @blur="v$.user.email.$touch" />
        <div v-show="v$.user.email.$error">
          {{ this.v$.user.email.email.$message }}
        </div>
        <button>valider</button>
      </form>
      <button @click="annuler">annuler</button>
    </div>
    <h2>Securité</h2>
    <p @click="showModale = true">Changer de mot passe</p>
    <div class="modal-overlay" v-show="showModale" @click="modale"></div>
    <transition name="slide" appear>
      <div class="modal" v-show="showModale">
        <div class="close" @click="modale">&#x2715;</div>
      
      </div>
    </transition>
    <p>Mot de passe : *********** <i class="fa-solid fa-marker"></i></p>
  </div> -->
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required, email, helpers, sameAs } from "@vuelidate/validators";
import axios from "axios";
const EditComponent = {
  data() {
    return {
      modaleEditEmail: false,
      modaleEdit: false,
      modaleEditPassword: false,
      modaleDeleteUser: false,
      show: null,
      user: {
        nom: "",
        prenom: "",
        email: "",
      },
      supprimer: "",
      nom: "",
      prenom: "",
      email: "",
      showModale: false,
      securite: {
        ancien: "",
        password: "",
        password_confirmation: "",
      },
      nbrCharPassword: 8,
      verifMaj: /^(?=.*?[A-Z]).{1,}$/,
      verifNumber: /^(?=.*?[0-9]).{1,}$/,
      verifSpecial: /^(?=.*?[#?!§€/¤£@$<>%^&*§¤:;,-]).{1,}$/,
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    return {
      user: {
        nom: { required: helpers.withMessage("Nom requis", required) },
        prenom: { required: helpers.withMessage("Prénom requis", required) },
        email: {
          required,
          email: helpers.withMessage("Email incorrect", email, required),
        },
      },
      securite: {
        password: {
          required,
          checkPassword: helpers.regex(
            /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!§€/¤£@$<>%^&*§¤:;,-]).{8,}$/
          ),
        },
        password_confirmation: {
          sameAsPassword: helpers.withMessage(
            "le mot de passe est différent",
            sameAs(this.securite.password_confirmation)
          ),
        },
      },
    };
  },
  beforeMount() {
    this.profil();
  },
  methods: {
    closeModaleEmail() {
      this.email = this.user.email;
      this.modaleEditEmail = false;
    },
    closeModaleDelete() {
      this.supprimer = "";
      this.modaleDeleteUser = false;
    },
    closeModaleEdit() {
      this.prenom = this.user.prenom;
      this.nom = this.user.nom;
      this.modaleEdit = false;
    },
    closeModalePassword() {
      this.securite.ancien = "";
      this.securite.password = "";
      this.securite.password_confirmation = "";
      this.modaleEditPassword = false;
    },

    modale() {
      this.showModale = false;
      this.securite.ancien = "";
      this.securite.password = "";
      this.securite.password_confirmation = "";
    },
    async profil() {
      const res = await axios.get("api/users");
      this.user.nom = res.data.nom;
      this.user.prenom = res.data.prenom;
      this.user.email = res.data.email;
      this.nom = res.data.nom;
      this.prenom = res.data.prenom;
      this.email = res.data.email;
    },
    check() {
      this.v$.$touch();
      !this.v$.user.nom.$error &&
      !this.v$.user.prenom.$error &&
      !this.v$.user.email.$error
        ? this.envoieForm()
        : console.log("erruer");
    },
    verifPassword() {
      this.v$.$touch();
      !this.v$.securite.password.$error &&
      !this.v$.securite.password_confirmation.$error
        ? this.envoiePassword()
        : console.log("erreur");
    },
    async envoieFormEmail() {
      const res = await axios.put("api/users/email", { email: this.email });
      console.log(res);
      if (res.status === 200) {
        localStorage.clear();
        this.show = "a";
        // setTimeout(function () {
        //   location.href = "https://mail.google.com/mail/u/0/?pli=1#inbox";
        // }, 5000);
        setTimeout(() => {
          this.$router.push("/projet");
        }, 5000);
      }
    },
    async envoieForm() {
      const res = await axios.put("api/users", {
        nom: this.nom,
        prenom: this.prenom,
      });
      console.log(res);
      this.user.prenom = res.data.prenom;
      this.user.nom = res.data.nom;
    },
    async envoiePassword() {
      const res = await axios.put("api/users/password", this.securite);
      console.log(res);
    },
    async deleteUser() {
      if (this.supprimer == "supprimer le compte") {
        const res = await axios.delete("api/users");
        if (res.status === 200) {
          localStorage.clear();
          this.$router.push("/");
        }
      }
    },
  },
};
export default EditComponent;
</script>

<style scoped>
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
.modale-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
  height: 100%;
}
form {
  display: flex;
  flex-direction: column;
}
.close {
  display: flex;
  justify-content: flex-end;
  content: "&#10006";
}
.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 98;
  background-color: rgba(0, 0, 0, 0.3);
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