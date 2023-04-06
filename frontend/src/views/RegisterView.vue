<template>
  <div class="main-wrapper">
    <h1 class="title">Register</h1>
    <div class="register-container">
      <form class="register-form" action="" method="post" @submit.prevent>
        <p class="termsMessage">
          By continuing, you are setting up a GameTracker account and agree to
          our
          <u>User Agreement</u> and <u>Privacy Policy</u>.
        </p>
        <div class="input-group">
          <label for="email">Username</label>
          <input
            v-model="username"
            type="username"
            id="username"
            placeholder="4-12 characters"
          />
        </div>
        <div class="input-group">
          <label for="email">Email</label>
          <input
            v-model="email"
            type="email"
            id="email"
            placeholder="email@address.com"
          />
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input
            v-model="password"
            type="password"
            id="password"
            placeholder="6-10 characters"
          />
        </div>
        <div class="input-group">
          <label for="passwordConfirmation">Password confirmation</label>
          <input
            v-model="passwordConfirmation"
            type="password"
            id="passwordConfirmation"
            placeholder="Repeat your password"
          />
        </div>

        <button @click="register" type="button">Register</button>
        <p class="loginMessage">
          Already on GameTracker?
          <router-link to="/login">Login</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "RegisterView",
  data() {
    return {
      username: null,
      email: null,
      password: null,
      passwordConfirmation: null,
    };
  },
  methods: {
    async register() {
      console.log("teste se botão funciona");
      //Lembrar de ajustar para tratar erros e coisas invalidas, mas por hora fazer com se tudo fosse perfeito e nenhum user mal intencionado
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}register`,
          {
            username: this.username,
            email: this.email,
            password: this.password,
            password_confirmation: this.passwordConfirmation,
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log(response.data);
        this.redirectToLoginPage;
      } catch (error) {
        //caso haja erro
        console.log(error.response.data);
      }
    },
    redirectToGamePage() {
      console.log("redirecionado!");
    },
  },
};
</script>

<style scoped>
.termsMessage {
  font-size: 12px;
  text-align: center;
  margin-bottom: 10px;
}
.loginMessage {
  font-weight: 800;
  font-size: 16px;
  align-self: center;
  padding: 10px;
}
.loginMessage a {
  color: #1abc9c;
  text-decoration: none;
  transition: 0.4s;
}
.loginMessage a:hover {
  color: #fff;
}
label {
  font-weight: 800;
  font-size: 20px;
}
.title {
  font-size: 32px;
  font-weight: 800;
  text-align: center;
  width: 100%;
  margin-top: 10px;
}
.register-container {
  background: rgba(48, 25, 189, 0.62);
  border-radius: 15px;
  margin: auto;
  margin-top: 10px;
  width: 420px;
  height: 640px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40px;
}
.input-group {
  display: flex;
  flex-direction: column;
  width: 100%;
}
.input-group input {
  height: 40px;
  margin-bottom: 30px;
  padding: 20px;
  font-size: 18px;
}
.register-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  width: 290px;
}
.register-form button {
  background: #1abc9c;
  border-radius: 35px;
  height: 50px;
  width: 250px;
  align-self: center;
  cursor: pointer;
}
</style>
