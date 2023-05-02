<template>
  <div class="main-wrapper">
    <Message :message="message" />
    <h1 class="title">Login</h1>
    <div class="login-container">
      <form class="login-form" action="" method="post" @submit.prevent>
        <p class="main-error" v-if="error">*{{ error }}</p>
        <div class="input-group">
          <div class="label-container">
            <label for="email"><i class="fas fa-at"></i> Email</label>
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
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <span v-if="passwordError" class="error">*{{ passwordError }}</span>
          </div>

          <input
            v-model="password"
            type="password"
            id="password"
            placeholder="6-10 characters"
          />
        </div>

        <button @click="login" type="button">
          <i class="fas fa-sign-in-alt"></i> Login
        </button>
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
import Message from "../../components/Tools/Message.vue";
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
        localStorage.setItem("personal_token", response.data.personal_token);
        //chama a função do store.js que é a setlogged que importei no method
        this.$store.commit("login", true);
        console.log(this.$store.state.personal_token);
        //redirecionar para algum lugar pois deu certo o login!
        let redirect;
        if (this.$route.query.register == "true") {
          console.log("teste 2");
          redirect = this.$route.query.redirect;
          this.$router.push({
            path: "/welcome",
            query: {
              redirect: redirect,
            },
          });
        } else {
          redirect = this.$route.query.redirect || "/";
          this.$router.push({
            path: redirect,
          });
        }
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
  font-size: 1.4vh;
  font-weight: 500;
  text-shadow: 1px 1px 1px #000000;
}
.registerMessage {
  font-weight: 800;
  font-size: 1.8vh;
  align-self: center;
  padding: 2vh 2vw;
  text-align: center;
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
  font-size: 1.8vh;
  display: inline;
}
.title {
  font-size: 5vh;
  font-weight: 800;
  text-align: center;
  width: 100%;
  margin-top: 10px;
}
.login-container {
  background: rgba(48, 25, 189, 0.62);
  border-radius: 20px;
  margin: auto;
  margin-top: 1vh;
  width: 30vw;
  height: 60vh;
  max-height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10vh;
}
.input-group {
  width: 90%;
  max-width: 100%;
}
.input-group input {
  width: 100%;
  height: 6vh;
  margin-bottom: 3.3vh;
  padding: 2vh 1.2vw;
  font-size: 2vh;
  align-self: center;
}
.login-form {
  margin: 0 auto;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 75%;
}
.login-form button {
  background: #1abc9c;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 15vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}
.login-form button:hover {
  color: #4c1bbc;
}
/* */
@media screen and (max-width: 991px) {
  .login-container {
    width: 70vw;
  }
  .input-group {
    width: 70%;
  }
  .input-group input {
    width: 100%;
  }
  .label {
    margin-left: 0;
  }
  .login-form button {
    width: 30vw;
    height: 100%;
  }
}
@media screen and (max-width: 768px) {
  .title {
    font-size: 4.3vh;
  }
  .login-container {
    width: 85vw;
  }
  .login-form {
    width: 80%;
  }
  .login-form button {
    width: 35vw;
    height: 100%;
  }
  .input-group {
    width: 70vw;
  }
  label {
    font-weight: 600;
    font-size: 1.7vh;
  }
  input {
    width: 100%;
  }
  .error {
    font-size: 1.2vh;
    font-weight: 300;
    text-shadow: 1px 1px 1px #000000;
  }
}
</style>
