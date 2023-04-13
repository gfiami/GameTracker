<template>
  <div class="main-wrapper">
    <Message :message="message" />
    <h1 class="title">Login</h1>
    <div class="login-container">
      <form class="login-form" action="" method="post" @submit.prevent>
        <p class="main-error" v-if="error">*{{ error }}</p>
        <div class="input-group">
          <div class="label-container">
            <label for="email">Email</label>
            <span v-if="emailError" class="error">*{{ emailError }}</span>
          </div>
          <input
            v-model="email"
            type="email"
            id="email"
            placeholder="email@address.com"
          />
        </div>
        <div class="input-group">
          <div class="label-container">
            <label for="password">Password</label>
            <span v-if="passwordError" class="error">*{{ passwordError }}</span>
          </div>

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
import Message from "../components/Message.vue";
//isso serve para usar as "mutations" do store.js, importando elas com o mapMutations
import { mapMutations } from "vuex";

export default {
  name: "LoginView",
  data() {
    return {
      email: null,
      password: null,
      message: this.$route.query.message || null,
      error: null,
      emailError: null,
      passwordError: null,
    };
  },
  components: {
    Message,
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
        //definindo localStorage (ajustar depois para que tenha um tempo de expiração para deslogar automaticamente)
        localStorage.setItem("gameTrackerUserToken", response.data.user.name);
        localStorage.setItem("user_id", response.data.user.id);

        //chama a função do store.js que é a setlogged que importei no method
        this.$store.commit("login", true);
        //redirecionar para algum lugar pois deu certo o login!
        this.$router.push({
          path: "/",
          query: {
            loginSuccess: "true",
            messageLogin: `${this.$store.state.token} `,
          },
        });
      } catch (error) {
        console.log(error);
        console.log(error.response.data.message); //aqui mostra minha mensagem definida no backend
        console.log(error.response.data.errors); //aqui mostra oque foi errado, caso senha seja invalida ou email invalido
        if (
          "Login failed, please check your credentials" ==
          error.response.data.message
        ) {
          this.error = error.response.data.message;
          this.emailError = null;
          this.passwordError = null;

          this.email = null;
          this.password = null;

          return false;
        }
        if (error.response.data.errors.email !== undefined) {
          this.emailErrors(error.response.data.errors.email);
          this.email = null;
        } else {
          this.emailError = null;
        }

        if (error.response.data.errors.password !== undefined) {
          console.log("há password error");
          this.passwordErrors(error.response.data.errors.password);
          this.password = null;
        } else {
          this.passwordError = null;
        }
      }
    },
    emailErrors(error) {
      this.emailError = error[0];
    },
    passwordErrors(error) {
      this.passwordError = error[0];
      console.log(this.passwordError);
    },
  },
};
</script>

<style scoped>
.label-container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
}
.main-error {
  text-align: center;
  color: #d9ff42;
  font-size: 16px;
  font-weight: 700;
}
.error {
  color: #d9ff42;
  font-size: 12px;
  font-weight: 500;
}
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
