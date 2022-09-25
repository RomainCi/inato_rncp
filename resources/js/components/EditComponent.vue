<template>
  <div class="bigContainer">
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
        <label for="nom">nom</label>
        <input type="text" v-model="nom" />
        <!-- <div v-show="v$.user.nom.$error">
          {{ this.v$.user.nom.required.$message }}
        </div> -->

        <label for="prenom">prénom</label>
        <input type="text" v-model="prenom" />
        <!-- <div v-show="v$.user.prenom.$error">
          {{ this.v$.user.prenom.required.$message }}
        </div> -->

        <button class="buttonModal">valider</button>
      </form>
    </div>
    <div v-show="modaleEditEmail" class="modal">
      <form @submit.prevent="envoieFormEmail">
        <label for="email">email</label>
        <input type="email" v-model="email" />
        <p v-show="show == 'a'">Vous avez 5 min pour valider votre email</p>
        <button class="buttonModal">valider</button>
      </form>
    </div>
    <div v-show="modaleEditPassword" class="modal">
      <form @submit.prevent="envoiePassword">
        <label for="ancien">ancien mot de passe</label>
        <input type="password" v-model="securite.ancien" />

        <label for="password">nouveau mot de passe</label>
        <input type="password" v-model="securite.password" />

        <label for="confirmation">confirmation du mot de passe</label>
        <input type="password" v-model="securite.password_confirmation" />

        <button class="buttonModal">valider</button>
      </form>
    </div>
    <div v-show="modaleDeleteUser" class="modal">
      <form @submit.prevent="deleteUser">
        <label>Ecrivez supprimer le compte </label>
        <input type="text" v-model="supprimer" />
        <button class="buttonModal">valider</button>
      </form>
    </div>
    <div class="mainContainer">
      <img
        class="imageMobile"
        src="../assets/photoProfil.png"
        alt="photoProfil"
      />
      <div class="container">
        <div class="containerInfo">
          <img
            class="imageBureau"
            src="../assets/photoProfil.png"
            alt="photoProfil"
          />
          <div class="bureauInfo">
            <div class="containerEditP">
              <p>mes informations</p>
              <button @click="modaleEdit = true">edit</button>
            </div>
            <p class="info">Nom: {{ user.nom }}</p>
            <p class="info">Prénom: {{ user.prenom }}</p>
          </div>
        </div>
        <div class="containerBureau">
          <div class="bureauSecu">
            <div class="containerInfo secu">
              <div class="containerSecurite">
                <p class="securite">sécurité</p>
              </div>
              <div class="editMail">
                <p class="infoEmail">Email : {{ user.email }}</p>
                <button class="buttonEmail" @click="modaleEditEmail = true">
                  edit
                </button>
              </div>
            </div>
            <div class="buttonPassword">
              <button @click="modaleEditPassword = true">
                Changer de mot de passe
              </button>
              <button @click="modaleDeleteUser = true">
                supprimer le compte
              </button>
            </div>
          </div>
          <div class="containerImage">
            <p class="compte">compte</p>
            <img src="../assets/edit.png" alt="edit" />
          </div>
        </div>
      </div>
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
.imageBureau {
  display: none;
}
.containerImage {
  display: none;
}
.containerBureau {
  width: 100%;
}
.mainContainer {
  background-color: white;
  margin-top: 45px;
  margin-left: 5%;
  margin-right: 5%;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  padding: 15px;
  justify-content: space-around;
}
.mainContainer img {
  height: 150px;
  width: 150px;
  align-self: center;
}
.mainContainer .containerEditP {
  display: flex;
  margin-bottom: 15px;
  justify-content: space-between;
  width: 100%;
  width: 100%;
}

.mainContainer .container {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.containerInfo {
  display: flex;
  flex-direction: column;
  width: 100%;
}

p {
  margin: 0px;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9rem;
  color: #1ea3dc;
  text-transform: uppercase;
  align-self: center;
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
  margin-left: 15px;
  text-transform: uppercase;
  text-align: center;
}
/* .containerSecurite {
  display: flex;
  justify-content: space-around;
} */

.buttonEmail {
  font-size: 0.6rem;
}
.info {
  font-size: 0.7rem;
  color: black;
  margin-bottom: 15px;
  width: 100%;
}
.infoEmail {
  font-size: 0.7rem;
  color: black;
}
.securite {
  margin-bottom: 15px;
}
.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 99;
  filter: blur(0px) !important;
  width: 75vw;
  height: 30vh;
  max-width: 600px;
  max-height: 600px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}
.editMail {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
}
.buttonPassword {
  width: 70%;
}
.buttonPassword button {
  margin: 0px;
  font-size: 0.6rem;
  width: 100%;
  margin-bottom: 15px;
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
  justify-content: space-evenly;
  height: 100%;
  width: 100%;
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
  max-width: 400px;
  background-color: #fff;
  border-radius: 16px;

  padding: 25px;
}
.modal form label {
  font-family: "Lexend Mega", sans-serif;
  color: #1ea3dc;
  text-align: center;
  font-size: 0.9em;
}
.modal form input {
  background-color: #d3edf8;
  height: 1.2em;
  font-family: "Lexend Mega", sans-serif;
  font-size: 0.9em;
  border-radius: 5px;
  outline: none;
  border: 2px solid #d3edf8;
}
.buttonModal {
  margin-left: 0px;
  font-size: 1em;
}
@media only screen and (min-width: 1100px) {
  .mainContainer img {
    height: 200px;
    width: 200px;
    align-self: center;
  }
  .mainContainer {
    height: 60vh;
    margin-left: auto;
    margin-right: auto;
    border-radius: 20px;
    width: 851px;
  }
  .containerImage {
    display: flex;
  }
  .imageMobile {
    display: none;
  }
  .imageBureau {
    display: flex;
  }
  .containerInfo {
    display: flex;
    flex-direction: row;
  }
  .containerBureau {
    display: flex;
    height: 60%;
  }
  .container {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .bureauInfo {
    align-self: center;
  }
  .bureauSecu {
    align-self: center;
  }
  .secu {
    display: flex;
    flex-direction: column;
  }
  p {
    font-size: 1.4rem;
  }
  .bigContainer {
    display: flex;
    justify-content: calc();
  }
  .compte {
    position: relative;
    left: 135px;
    top: 86px;
  }
  .info,
  .infoEmail {
    font-size: 1rem;
  }
  .compte img {
    height: 250px;
    width: 250px;
  }
  .buttonPassword button {
    font-size: 1rem;
  }
}
</style>