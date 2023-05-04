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
            <label for="username"><i class="fas fa-user"></i> Username</label>
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
        <div class="input-group">
          <div class="label-container">
            <label for="passwordConfirmation"
              ><i class="fas fa-lock"></i> Password confirmation</label
            >
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

        <button @click="register" type="button">
          <i class="fas fa-user-plus"></i> Register
        </button>
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
      this.usernameError = null;
      this.emailError = null;
      this.passwordError = null;
      this.confirmationError = null;

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
        const redirect = this.$route.query.redirect;
        this.$router.push({
          path: "/login",
          query: {
            redirect: redirect,
            register: true,
            loginSuccess: "true",
            message: "Your GameTracker account has been successfully created! ",
          },
        });
      } catch (error) {
        //caso haja erro
        console.log(error);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
        //email error
        if (error.response.data.errors.email !== undefined) {
          this.emailErrors(error.response.data.errors.email);
        }
        //username eror
        if (error.response.data.errors.username !== undefined) {
          this.usernameErrors(error.response.data.errors.username);
        }
        //password error
        if (error.response.data.errors.password !== undefined) {
          this.passwordErrors(error.response.data.errors.password);
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
        this.passwordError = error[0];
      } else this.passwordError = error[0];

      this.password = null;
      this.passwordConfirmation = null;
    },
  },
};
</script>

<style scoped>
.termsMessage {
  font-size: 1.5vh;
  text-align: center;
  margin: 2vh 0;
}
.termsMessage u {
  cursor: pointer;
}
.loginMessage {
  font-weight: 800;
  font-size: 1.8vh;
  align-self: center;
  padding: 2vh 2vw;
  text-align: center;
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
  align-items: flex-start;
}
label {
  font-weight: 800;
  font-size: 1.8vh;
  display: inline;
}
.error {
  color: #d9ff42;
  font-size: 1.4vh;
  font-weight: 500;
  text-shadow: 1px 1px 1px #000000;
}
.title {
  font-size: 5vh;
  font-weight: 800;
  text-align: center;
  width: 100%;
  margin-top: 10px;
}
.register-container {
  padding: 2vh 2vw;
  background: rgba(48, 25, 189, 0.62);
  border-radius: 20px;
  margin: auto;
  margin-top: 1vh;
  width: 40vw;
  height: 80vh;
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
.register-form {
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  max-width: 85%;
  max-height: 100%;
}
.register-form button {
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
.register-form button:hover {
  color: #4c1bbc;
}
@media screen and (max-width: 991px) {
  .register-container {
    width: 70vw;
  }
  .input-group {
    width: 70%;
  }
  .input-group input {
    width: 100%;
  }

  .register-form button {
    width: 30vw;
    height: 100%;
  }
}
@media screen and (max-width: 768px) {
  .title {
    font-size: 4.3vh;
  }
  .register-container {
    width: 85vw;
  }
  .register-form button {
    width: 35vw;
    height: 100%;
  }
  .input-group {
    width: 70vw;
  }
  .termsMessage {
    font-size: 1.5vh;
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
