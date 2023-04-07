<template>
  <div class="main-wrapper">
    <h1 class="title">Login</h1>
    <div class="login-container">
      <form class="login-form" action="" method="post" @submit.prevent>
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

        <button @click="login" type="button">Login</button>
        <p class="registerMessage">
          First time at GameTracker?
          <router-link to="/register">Register</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import router from "@/router";
//isso serve para usar as "mutations" do store.js, importando elas com o mapMutations
import { mapMutations } from "vuex";

export default {
  name: "LoginView",
  data() {
    return {
      email: null,
      password: null,
    };
  },
  methods: {
    //isso usa a mutation para importar a função que temos no store.js
    ...mapMutations(["login"]),
    //ai eu posso chamar a função com  this.$store.commit("login", true);

    //lembrar de ajustar para tratar erros também aqui no front
    async login() {
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}signin`,
          {
            email: this.email,
            password: this.password,
          }
        );
        //definindo localStorage
        localStorage.setItem("gameTrackerUserToken", response.data.token);

        console.log(response.data.token + "aqui é o token!"); //token criado no login
        console.log(response.data.user);

        //chama a função do store.js que é a setlogged que importei no method
        this.$store.commit("login", true);
        //redirecionar para algum lugar pois deu certo o login!
        this.$router.push("/");
      } catch (error) {
        //console.log(error);
        console.log(error.response.data.message); // aqui mostra a mensagem que eu defini!
        console.log(error.response.data.errors); //aqui mostra oque foi errado, caso senha seja invalida ou email invalido
      }
    },
  },
};
</script>

<style scoped>
.registerMessage {
  font-weight: 800;
  font-size: 16px;
  align-self: center;
  padding: 10px;
}
.registerMessage a {
  color: #1abc9c;
  text-decoration: none;
  transition: 0.4s;
}
.registerMessage a:hover {
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
.login-container {
  background: rgba(48, 25, 189, 0.62);
  border-radius: 15px;
  margin: auto;
  margin-top: 10px;
  width: 420px;
  height: 440px;
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
.login-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  width: 290px;
}
.login-form button {
  background: #1abc9c;
  border-radius: 35px;
  height: 50px;
  width: 250px;
  align-self: center;
  cursor: pointer;
}
</style>
