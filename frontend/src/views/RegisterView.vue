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
          <div class="label-container">
            <label for="username">Username</label>
            <span v-if="usernameError" class="error">*{{ usernameError }}</span>
          </div>

          <input
            v-model="username"
            type="username"
            id="username"
            placeholder="4-12 characters"
          />
        </div>
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
        <div class="input-group">
          <div class="label-container">
            <label for="passwordConfirmation">Password confirmation</label>
            <span v-if="confirmationError" class="error"
              >*{{ confirmationError }}</span
            >
          </div>

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
      emailError: null,
      usernameError: null,
      passwordError: null,
      confirmationError: null,
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
        console.log(response);
        this.$router.push({
          path: "/login",
          query: {
            message: "Your GameTracker account has been successfully created!",
          },
        });
      } catch (error) {
        //caso haja erro
        //console.log(error.response.data.message);
        //console.log(error.response.data.validation);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
        //email error
        if (error.response.data.errors.email !== undefined) {
          this.emailErrors(error.response.data.errors.email);
        } else {
          this.emailErrors = null;
        }
        //username eror
        if (error.response.data.errors.username !== undefined) {
          this.usernameErrors(error.response.data.errors.username);
        } else {
          this.usernameErrors = null;
        }
        //password error
        if (error.response.data.errors.password !== undefined) {
          this.passwordErrors(error.response.data.errors.password);
        } else {
          this.passwordErrors = null;
        }
      }
    },
    emailErrors(error) {
      this.emailError = error[0];
      this.email = null;
    },
    usernameErrors(error) {
      this.usernameError = error[0];
      this.username = null;
    },
    passwordErrors(error) {
      if (error.length == 2) {
        this.passwordError = error[0];
        this.confirmationError = error[1];
      } else if (error[0] == "The password confirmation does not match.") {
        this.confirmationError = error[0];
      } else this.passwordError = error[0];

      this.password = null;
      this.passwordConfirmation = null;
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
.termsMessage u {
  cursor: pointer;
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
.label-container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
}
label {
  font-weight: 800;
  font-size: 20px;
  display: inline;
}
.error {
  color: #d9ff42;
  font-size: 12px;
  font-weight: 500;
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
