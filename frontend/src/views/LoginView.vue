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

        <button type="button">Login</button>
        <p class="registerMessage">
          First time at GameTracker?
          <router-link to="/register">Register</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoginView",
  data() {
    return {};
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
