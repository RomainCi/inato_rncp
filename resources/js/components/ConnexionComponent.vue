<template>
  <div class="parent">
    <div class="modale-overlay" v-show="modale" @click="modaleEmail"></div>
    <div v-show="modale" class="modal">
      <form class="containerModale" @submit.prevent="forgetPassword">
        <label for="email">EMAIL</label>
        <input
          :class="emailF"
          type="email"
          v-model="email"
          @blur="v$.email.$touch"
        />
        <div class="error" v-if="v$.email.$error">
          {{ (emailF = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (emailF = "correct") }}
        </div>
        <div class="show" v-if="showPassword == 'a'">
          VOUS AVEZ 5 MINUTES POUR VALIDER VOTRE EMAIL
        </div>
        <div class="show" v-if="showPassword == 'b'">EMAIL INCONNUE</div>
        <button>VALIDER</button>
      </form>
    </div>
    <div class="parentContainer">
      <form class="container" @submit.prevent="submitForm">
        <label for="email">EMAIL</label>
        <input
          :class="emailC"
          type="mail"
          v-model="user.email"
          @blur="v$.user.email.$touch"
        />
        <div class="error" v-if="v$.user.email.$error">
          {{ (emailC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (emailC = "correct") }}
        </div>
        <label for="password">MOT DE PASSE</label>
        <input
          @blur="v$.user.password.$touch"
          :class="passwordC"
          type="password"
          v-model="user.password"
        />
        <div class="error" v-if="v$.user.password.$error">
          {{ (passwordC = "pasCorrect") }}
        </div>
        <div class="error" v-else>
          {{ (passwordC = "correct") }}
        </div>
        <button>CONNEXION</button>
        <div class="child">
          <p @click="modale = true">MOT DE PASSE OUBLIÉ</p>
        </div>
      </form>
    </div>
    <div class="containerChild">
      <p>DEVENIR MEMBRE</p>
      <button @click="inscription">INSCRIPTION</button>
    </div>
    <div class="show" v-show="show == 'a'">
      <p>MOT DE PASSE INCORRECT OU EMAIL</p>
    </div>
  </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { helpers, required, email } from "@vuelidate/validators";
import axios from "axios";
import router from "../router";
const ConnexionComponent = {
  props: {
    titre: String,
    inscription: Function,
  },
  data() {
    return {
      modale: false,
      v$: null,
      user: {
        email: "",
        password: "",
      },
      emailC: "correct",
      passwordC: "correct",
      emailF: "correct",
      show: "",
      email: "",
      showPassword: "",
    };
  },
  validations() {
    return {
      email: { required, email },
      user: {
        email: { required, email },
        password: {
          required,
          checkPassword: helpers.regex(
            /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!§€/¤£@$<>%^&*§¤:;,-]).{8,}$/
          ),
        },
      },
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  mounted() {
    if (localStorage.getItem("authAcces")) {
      router.push({ path: "projet" });
    } else {
      console.log("pas oki");
    }
  },
  methods: {
    submitForm() {
      this.v$.$touch();
      !this.v$.user.email.$error && !this.v$.user.password.$error
        ? this.connexion()
        : console.log("erreur");
    },
    /////////////////REQUETE//////////////
    async connexion() {
      const csrf = await axios.get("sanctum/csrf-cookie");
      console.log("csrf", csrf);
      const res = await axios.post("api/login", this.user);
      console.log(res, "je suis avajt la condiotion");
      if (res.data.message == "erreur") {
        console.log(res, "je suis dans l'erreur");
        this.show = "a";
      } else if (res.data.message == "success") {
        console.log(res, "je suis pas l'erreur");
        localStorage.setItem("authAcces", "acces");
        location.reload();
      }
    },
    async forgetPassword() {
      const res = await axios.post("api/forget", { email: this.email });
      console.log(res);
      if (res.data.message == "succes") {
        this.showPassword = "a";
      } else {
        this.showPassword = "b";
      }
    },
    modaleEmail() {
      this.email = "";
      this.modale = false;
    },
  },
};
export default ConnexionComponent;
</script>

<style scoped>
.parent {
  font-size: 16px;
  height: 40.092vh;
  width: 30.598958333333332vw;
}
.parent .parentContainer {
  background-color: white;
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 1.75em;
}
.container {
  /* width: 24.4140625vw; */
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  font-size: 16px;
  width: 100%;
}
.modal .containerModale label {
  line-height: 2.188em;
  font-size: 0.9em;
  font-family: "Lexend Mega", sans-serif;
  align-self: center;
}

.container label {
  line-height: 2.188em;
  font-size: 0.9em;
  font-family: "Lexend Mega", sans-serif;
  align-self: center;
}
.container div {
  align-self: center;
}
.error {
  display: none;
}
.container .child p {
  font-size: 10px;
  font-family: "Lexend Mega", sans-serif;
  margin: 0px;
  margin-top: 1.5rem;
  width: fit-content;
  color: #1ea3dc;
  cursor: pointer;
}
.container button {
  font-size: 0.9rem;
  font-family: "Lexend Mega", sans-serif;
  margin-top: 1.5rem;
  width: 9.375rem;
  align-self: center;
  border-radius: 6.25em;
  border: none;
  cursor: pointer;
  padding: 0.5em;
  background-color: #1ea3dc;
  color: white;
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
.show {
  margin-top: 3rem;
  text-align: center;
  color: #1ea3dc;
  background-color: white;
  border-radius: 6.25rem;
  font-size: 0.9rem;
  font-family: "Lexend Mega", sans-serif;
}
.modale-overlay {
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
  z-index: 101;
  width: 100%;
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;
  padding: 25px;
}
.modal .containerModale {
  display: flex;
  flex-direction: column;
}
.parent .containerChild {
  display: flex;
  justify-content: space-around;
}
.parent .containerChild p {
  margin: 0px;
  margin-top: 4rem;
  align-self: center;
  color: white;
  font-size: 0.9rem;
  font-family: "Lexend Mega", sans-serif;
}
.parent .containerChild button {
  margin-top: 4rem;
  background-color: white;
  color: #1ea3dc;
}
</style>
