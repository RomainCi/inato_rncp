<template>
  <div>
    <h2>{{ titre }}</h2>
    <form class="container" @submit.prevent="submitForm">
      <label for="email">Email</label>
      <input type="mail" v-model="user.email" @blur="v$.user.email.$touch" />
      <div v-if="v$.user.email.$error">Email invalide</div>

      <!-- <div v-for="error of v$.user.email.$error" :key="error.$uid">
				<div class="error-msg">{{ error.$message }}</div>
			</div> -->

      <label for="password">Password</label>
      <input
        type="password"
        v-model="user.password"
        @blur="v$.user.password.$touch"
      />
      <!-- <div class="error" v-if="this.v$.$invalid">Field is required</div> -->
      <div class="error" v-if="this.v$.user.password.$error">
        mot de passe trop court
      </div>

      <button>Valider</button>
    </form>
    <p>Vous n'avez pas de compte?</p>
    <p @click="inscription">Inscrivez-vous</p>
  </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { minLength, required, email } from "@vuelidate/validators";
const ConnexionComponent = {
  props: {
    titre: String,
    inscription: Function,
  },
  data() {
    return {
      v$: null,
      user: {
        email: "",
        password: "",
      },
    };
  },
  validations() {
    return {
      user: {
        email: { required, email },
        password: { required, minLength: minLength(6) },
      },
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  methods: {
    submitForm() {
      console.log(!this.v$.user.password.$invalid);
      console.log(this.v$.user.password.$required);
      console.log(!this.v$.$invalid);
    },
  },
};
export default ConnexionComponent;
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
}
</style>
