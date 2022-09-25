<template>
  <div class="parent">
    <div :class="containerParent">
      <form class="container" @submit.prevent="verification">
        <label for="nom">NOM</label>
        <input
          :class="nomC"
          type="text"
          v-model="user.nom"
          @blur="v$.user.nom.$touch"
        />
        <div class="error" v-if="v$.user.nom.$error">
          {{ (this.nomC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (this.nomC = "correct") }}
        </div>
        <label for="prenom">PRÉNOM</label>
        <input
          type="text"
          :class="prenomC"
          v-model="user.prenom"
          @blur="v$.user.prenom.$touch"
        />
        <div class="error" v-if="v$.user.prenom.$error">
          {{ (this.prenomC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (this.prenomC = "correct") }}
        </div>
        <label for="email">EMAIL</label>
        <input
          :class="emailC"
          type="text"
          v-model="user.email"
          @blur="v$.user.email.$touch"
        />
        <div class="error" v-if="v$.user.email.$error">
          {{ (this.emailC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (this.emailC = "correct") }}
        </div>
        <label for="password">MOT DE PASSE *</label>
        <input
          type="password"
          :class="passwordC"
          v-model="user.password"
          @blur="v$.user.password.$touch"
        />
        <div class="error" v-if="v$.user.password.$error">
          {{ (this.passwordC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (this.passwordC = "correct") }}
        </div>
        <label for="verifPassword">CONFIRMATION DE MOT DE PASSE</label>
        <input
          type="password"
          :class="confirmationC"
          v-model="user.password_confirmation"
          @blur="v$.user.password_confirmation.$touch"
        />
        <div class="error" v-if="v$.user.password_confirmation.$error">
          {{ (this.confirmationC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (this.confirmationC = "correct") }}
        </div>
        <p>* 8 LETTRES 1 MAJUSCULE 1 CHIFFRE 1 CARACTÈRE SPÉCIAL OBLIGATOIRE</p>
        <button v-if="showe == 'e'">INSCRIPTION</button>
      </form>
      <div v-if="show == 'a'" class="show">
        <p>VOUS AVEZ 5 MINUTES POUR VALIDER VOTRE INSCRIPTION</p>
      </div>
      <div v-if="show == 'b'" class="show">
        <p>CETTE EMAIL EST DÉJÀ UTULISÉ</p>
      </div>
      <div v-if="show == 'c'" class="show">
        <p>VALIDER VOTRE EMAIL</p>
      </div>
    </div>
    <div class="containerChild">
      <p>DÉJÀ MEMBRE ?</p>
      <button @click="connexion">CONNEXION</button>
    </div>
  </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required, email, sameAs, helpers } from "@vuelidate/validators";
import axios from "axios";
const InscriptionComponent = {
  props: {
    titre: String,
    connexion: Function,
  },
  data() {
    return {
      user: {
        nom: "",
        prenom: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
      nomC: "correct",
      prenomC: "correct",
      emailC: "correct",
      passwordC: "correct",
      confirmationC: "correct",
      showe: "e",
      show: "",
      containerParent: "containerParent",
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    return {
      user: {
        nom: { required },
        prenom: { required },
        email: {
          required,
          email,
        },

        password: {
          required,
          checkPassword: helpers.regex(
            /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!§€/¤£@$<>%^&*§¤:;,-]).{8,}$/
          ),
        },
        password_confirmation: {
          sameAsPassword: sameAs(this.user.password),
        },
      },
    };
  },
  methods: {
    verification() {
      this.v$.$touch();
      !this.v$.user.nom.$error;
      !this.v$.user.prenom.$error;
      !this.v$.user.email.$error &&
      !this.v$.user.password.$error &&
      !this.v$.user.password_confirmation.$error
        ? this.envoieInscription()
        : console.log("faux");
    },

    async envoieInscription() {
      console.log("premier");
      const promise = await axios.post("api/users", this.user);
      console.log(promise);
      console.log("deuxieme");
      if (promise.data.succes === "succes") {
        this.containerParent = "containerParentBig";
        this.show = "a";
        this.showe = "";
      } else if (promise.data.succes === "notSucces") {
        this.containerParent = "containerParentBig";
        this.show = "b";
      } else {
        this.containerParent = "containerParentBig";
        this.show = "c";
        this.showe = "";
      }
    },
  },
};
export default InscriptionComponent;
</script>

<style scoped>
.error {
  display: none;
}
.parent {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.parent .containerChild {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin-top: 4em;
  font-size: 16px;
}
.parent .containerChild p {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  color: white;
  margin: 0px;
  padding: 0.5em;
}
.parent .containerChild button {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 6.25em;
  padding: 0.5em;
  background-color: white;
  color: #1ea3dc;
  border: none;
  align-self: center;
  text-align: center;
  cursor: pointer;
}
.containerParent {
  background-color: white;
  height: 54vh;
  width: 30.598958333333332vw;
  border-radius: 1.75em;
  font-size: 16px;
}
.containerParentBig {
  background-color: white;
  height: 60vh;
  width: 30.598958333333332vw;
  border-radius: 1.75em;
  font-size: 16px;
}
.show {
  margin: 1.5rem;
}
.show p {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  margin: 0px;
  background-color: #1ea3dc;
  color: white;
  text-align: center;
  padding: 0.5em;
  border-radius: 6.25em;
}
.container {
  display: flex;
  flex-direction: column;
  text-align: center;
  padding: 1.5rem;
  justify-content: center;
  font-size: 16px;
  /* width: 24.4140625vw; */
}
.container label {
  font-size: 0.9em;
  line-height: 2.188em;
  font-family: "Lexend Mega", sans-serif;
}
.correct {
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
}
.pasCorrect {
  border-radius: 5px;
  outline: none;
  border: 2px solid red;
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
}
.container button {
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

.container p {
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.6em;
  margin: 0px;
  margin-top: 1vh;
}
</style>
