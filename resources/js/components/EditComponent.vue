<template>
  <div>
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
        <form class="formulaire" @submit.prevent="verifPassword">
          <label for="ancien">ancien mot de passe</label>
          <input type="text" v-model="securite.ancien" />

          <label for="password">nouveau mot de passe</label>
          <input
            type="text"
            v-model="securite.password"
            @blur="v$.securite.password.$touch"
          />
          <div class="error" v-if="v$.securite.password.$error">
            {{
              this.nbrCharPassword - this.securite.password.length > 0
                ? `il manque ${
                    this.nbrCharPassword - this.securite.password.length
                  } lettres`
                : ""
            }}

            {{
              this.verifMaj.test(this.securite.password)
                ? ""
                : "1 majuscule obligatoire"
            }}
            {{
              this.verifNumber.test(this.securite.password)
                ? ""
                : "1 chiffre obligatoire"
            }}
            {{
              this.verifSpecial.test(this.securite.password)
                ? ""
                : "1 caractere spécial obligatoire"
            }}
          </div>

          <label for="confirmation">confirmation du mot de passe</label>
          <input
            type="text"
            v-model="securite.password_confirmation"
            @blur="v$.securite.password_confirmation.$touch"
          />
          <div class="error" v-if="v$.securite.password_confirmation.$error">
            {{ this.v$.securite.password_confirmation.sameAsPassword.$message }}
          </div>
          <button>valider</button>
        </form>
      </div>
    </transition>
    <p>Mot de passe : *********** <i class="fa-solid fa-marker"></i></p>
  </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required, email, helpers, sameAs } from "@vuelidate/validators";
import axios from "axios";
const EditComponent = {
  data() {
    return {
      user: {
        nom: "",
        prenom: "",
        email: "",
      },
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
    modale() {
      this.showModale = false;
      this.securite.ancien = "";
      this.securite.password = "";
      this.securite.password_confirmation = "";
    },
    async profil() {
      const csrf = await axios.get("sanctum/csrf-cookie");
      console.log(csrf);
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
    async envoieForm() {
      const res = await axios.put("api/users", this.user);
      console.log(res);
    },
    async envoiePassword() {
      const res = await axios.put("api/users/password", this.securite);
      console.log(res);
    },
    annuler() {
      console.log("hey");
      this.user.nom = this.nom;
      this.user.prenom = this.prenom;
      this.user.email = this.email;
    },
  },
};
export default EditComponent;
</script>

<style scoped>
.formulaire {
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