<template>
  <div id="nav">
    <router-link id="website-name" to="/">GameTracker</router-link>
    <router-link to="/games">Games</router-link>
    <router-link class="nav-link" to="/login">Login</router-link>
    <router-link class="nav-link" to="/register">Register</router-link>
    <a @click="logout" v-if="userToken !== null && userToken !== undefined"
      >Logout</a
    >
  </div>
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      userToken: localStorage.getItem("gameTrackerUserToken"),
    };
  },
  methods: {
    logout() {
      localStorage.removeItem("gameTrackerUserToken");
      window.location.href = "/";
      window.location.reload();
    },
  },
};
</script>

<style scoped>
#nav {
  background-color: #6842ff;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 15px 50px;
}
#nav #website-name {
  font-size: 32px;
  font-weight: 800;
  margin: auto;
  margin-left: 0;
  color: #fff;
}
#nav a {
  color: #fff;
  text-decoration: none;
  margin: 10px;
  transition: 0.4s;
  cursor: pointer;
}
#nav a:hover {
  color: #161b3a;
}
#nav a.router-link-exact-active {
  color: #161b3a;
}
</style>
